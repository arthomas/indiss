<?php
/**
 * @version     2010-09-16
 * @author      Myriam Leggieri <myriam.leggieri@gmail.com>
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Myriam Leggieri, Patrick Lehner
 * @module      Log events manager: stores log events to csv file or to Mysql db and allows
 *              to perform searches over them by specify one or more values for any of their
 *              fields.
 *
 * @license     This program is free software: you can redistribute it and/or modify
 *              it under the terms of the GNU General Public License as published by
 *              the Free Software Foundation, either version 3 of the License, or
 *              (at your option) any later version.
 *
 *              This program is distributed in the hope that it will be useful,
 *              but WITHOUT ANY WARRANTY; without even the implied warranty of
 *              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *              GNU General Public License for more details.
 *
 *              You should have received a copy of the GNU General Public License
 *              along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");
defined("__DATABASE") or die("Database connection not included [" . __FILE__ . "]");

define("__LOGGER", 1);

class Logger {
    
    //---- Class constants --------------------------------------------------------------

    /** 
     * Maximum size of log file before rotation, in bytes.
     * @var int
     */
	const maxFilesize = 104857600;
	/**
	 * Delimiter for the search queries entered by the user.
	 * @var string
	 */
	const searchQueryDelimiter = ",";
	/**
	 * The prefix for INDISS log files.
	 * @var string
	 */
	const fileNamePrefix = "indiss_";
	/**
	 * The delimiter inserted between the root filename and the count (for archived files).
	 * @var string
	 */
	const fileNameDelimiter = "_";
	/**
	 * The suffix (including file extension) for log files
	 * @var string
	 */
	const fileNameSuffix = ".log";
	/**
	 * Prefix for log tables in the database.
	 * @var string
	 */
	const tablePrefix = "logs_";
	/**
	 * Cell delimiter for the CSV file. Note: changing this will make all previously
	 * created log files unreadable!
	 * @var string
	 */
	const csvDelimiter = ";";
	/**
	 * Directory within the INDISS folder to place the log files in. This folder must exist
	 * and be writable by PHP.
	 * @var string
	 */
	const logsFolder = "logs/";
	
	
    //---- Static properties ------------------------------------------------------------

	/**
	 * These are the values inserted below for $col_datetime, $col_user, $col_origin,
	 * $col_type and $col_info, respectively
	 * @var array
	 */
	public static $parameters = array('when', 'uid', 'origin', 'type', 'message');
	/**
	 * The log event levels. These will be translated to global constants; e.g. for the
	 * level 'error' with the index 3, a global constant names LEL_ERROR will be created.
	 * @var array
	 */
	public static $levels = array(
        0 => "debug",
        1 => "notice",
        2 => "warning",
        3 => "error",
        4 => "critical"
	);
	
	
    //---- Object properties ------------------------------------------------------------
	
	private $tablename = "logs";
	private $col_key = "id";
	private $col_datetime = null;
    private $col_user = null;
	private $col_origin = null;
	private $col_type = null;
	private $col_info = null;
	private $logs = array();
	private $liveEvents = array();
	
	
    //---- Object methods ---------------------------------------------------------------
    
    /**
     * Construct a logger object.
     */
    public function __construct() {
        $this->col_datetime = self::$parameters[0];
        $this->col_user = self::$parameters[1];
        $this->col_origin = self::$parameters[2];
        $this->col_type = self::$parameters[3];
        $this->col_info = self::$parameters[4];
    }
    

    /**
     * Format the date passed as $time, or the current time() if NULL is passed,
     * to be used in a MySQL query.
     * @param string $time The time stamp to be formatted, or NULL to use the
     * current system time. Defaults to NULL.
     * @return string Returns the formatted DATETIME string
     */
    private function datetimeFormatter($time=NULL){
        if (!is_null($time)){
            $ret = date("Y-m-d H:i:s", strtotime($time));
        }else{
            $ret = date("Y-m-d H:i:s");
        }
        return $ret;
    }
    
    
    /**
     * Construct the complete log file path, consisting of:
     * full base path to INDISS, the logs subfolder, the file name prefix,
     * the name of the log, the index (if any) and the file name suffix.
     * @param string $basename The name of the log
     * @param int $index The index of the file. Will be formatted as a
     * 3 character-wide number, if given. If NULL, will be ignored. Defaults
     * to NULL.
     */
    private function constructLogFileCompletePath($basename, $index = null){
        global $FBP;
        $indexbit = (is_null($index)) ? "" : self::fileNameDelimiter . sprintf("%03d", $index);
        return $FBP . self::logsFolder . self::fileNamePrefix . $basename . $indexbit . self::fileNameSuffix;
    }
    
    /**
     * Create a new log file if necessary, and move an existing log file exceeding
     * maximum file size to an archive location.
     * @param string $basename The log name.
     * @return bool Returns true on success or false on failure.
     */
    private function createNewLogFile($basename){
        //check if file exists and, if so, if it exceeds the max file size
        $fn = $this->constructLogFileCompletePath($basename);
        if (file_exists($fn) && filesize($fn) > self::maxFilesize) { //if so, rename it to the next unused numbered file
            $i = 1;
            while (file_exists($fn2 = $this->constructLogFileCompletePath($basename, $i))){
                $i++;
            }
            if (rename($fn, $fn2) === false)
                return false;
        }

        if (!file_exists($fn)) {
            //create the new log file, then close it again
            $f = fopen($fn, "w");
            if ($f === false) {
                trigger_error("Logger::createNewLogFile(): Cannot create new log file", E_USER_ERROR);
                return false;
            }
            fclose($f);
        }
        return true;
    }

	/**
	 * Append a new event to the log file.
	 * @param string $basename The basename of the file to which will be added the relative
	 * path, prefix and suffix as defined the class constants.
	 * @param array $row The data to be written to the file.
	 * @return bool Returns true on success or false on failure.
	 */
	private function appendEventToCsv($basename, $row){
		$ret = true;

		$f = fopen($this->constructLogFileCompletePath($basename), "a");
		if ($f === false) {
			return false;
		} else {
			$ret = fputcsv($f, $row, self::csvDelimiter) !== false;
		}
		fclose($f);
		return $ret;
	}

    /**
     * Read a CSV log file into an array.
     * @param string $filename The path to the log file
     * @return 2-D array, where the first dimension is the line and the second dimension is the column in the CSV file.
     */
    private function getCsvFileContent($filename){
        $ret = array();
        if (!$f = fopen($filename, "r")) {
            trigger_error("Logger::getCsvFileContent(): Cannot open file in read mode ($filename)", E_USER_ERROR);
            return null;
        } else {
            while (($csv_line = fgetcsv($f, 1000, self::csvDelimiter)) !== FALSE) {
                $ret[] = $csv_line;
            }
        }
        fclose($f);
        return $ret;
    }

    /**
     * Read the current and all archived log files of a certain log into an array.
     * @return One big array with all existing log files appended one after another (old to new)
     */
    private function getAllCsvFilesContent($basename){
        global $FBP;
        $result_array = array();
        $i = 1;
        while ( file_exists($fn=$this->constructLogFileCompletePath($basename, $i++)) )
        {
            $result_array = array_merge($result_array, $this->getCsvFileContent($fn));
        }
        $result_array = array_merge($result_array, $this->getCsvFileContent($this->constructLogFileCompletePath($basename)));
        return $result_array;
    }

    private function checkTimeMatch($csv_time, $timebefore, $timeafter){
        $eligible = true;
        $csv_time = strtotime($csv_time);
        if ($eligible && !is_null($timebefore)){
            if ($csv_time > strtotime($timebefore)){
                $eligible = false;
            }
        }
        if ($eligible && !is_null($timeafter)){
            if ($csv_time < strtotime($timeafter)){
                $eligible = false;
            }
        }
        return $eligible;
    }

    private function checkInfoMatch($info, $keywords, $bool_keywords){
        $eligible = true;
        $splitted = explode(Logger::searchQueryDelimiter, $keywords);
        switch(strtolower($bool_keywords)){
            case "and":
                for ($i=0; $i<count($splitted)&&$eligible ;$i++){
                    if (!strstr($info, $splitted[$i])){
                        $eligible = false;
                    }
                }
                break;
            case "or":
                $eligible = false;
                for ($i=0; $i<count($splitted)&&!$eligible ;$i++){
                    if (stristr($info, $splitted[$i]) !== false){
                        $eligible = true;
                    }
                }
                break;
        }

        return $eligible;
    }

    private function checkUsersMatch($field, $users){
        $eligible = false;
        $splitted = explode(Logger::searchQueryDelimiter, $users);
        for ($i=0; $i<count($splitted)&&!$eligible ;$i++){
            if (strcmp($field, $splitted[$i]) == 0){
                $eligible = true;
            }
        }
        return $eligible;
    }

    private function checkOriginsMatch($field, $origins){
        $eligible = false;
        $splitted = explode(Logger::searchQueryDelimiter, $origins);
        for ($i=0; $i<count($splitted)&&!$eligible ;$i++){
            if (strcmp($field, $splitted[$i]) == 0){
                $eligible = true;
            }
        }
        return $eligible;
    }

    private function checkTypesMatch($field, $types){
        $eligible = false;
        $splitted = explode(Logger::searchQueryDelimiter, $csv_data[$ind]);
        for ($i=0; $i<count($splitted)&&!$eligible ;$i++){
            if (strcmp($field, $splitted[$i]) == 0){
                $eligible = true;
            }
        }
        return $eligible;
    }
    

    /**
     * split a string that contains values separated by a specific delimiter
     * each value is included in a boolean expression using the specified
     * boolean operator and is related to a column of the logs mysql table.
     * @param unknown_type $toSplit
     * @param unknown_type $delimiter
     * @param unknown_type $bool
     * @param unknown_type $column
     * @return unknown_type
     */
    private function splitIntoQuery($toSplit, $connector = " OR ", $column, $useLikeOp=false){
        $splitted = explode(self::searchQueryDelimiter, $toSplit);
        foreach ($splitted as &$item) {
            if ($useLikeOp) {
                $item = " $column like '%$item%' ";
            } else {
                $item = " $column = '$item' ";
            }
        }
        $glued = implode($connector, $splitted);
        return "($glued) ";
    }
    
    
    
    public function addLog($name, $minLevel, $logLive = false, $logToFile = null, $logToDb = null, $isDebug = false) {
        global $db;
        foreach ($this->logs as $l)
            if ($l["name"] == $name)
                return false;
        if ( !$logLive && !$logToFile && !$logToDb ) {
            trigger_error(__METHOD__ . "(): this logger will do nothing", E_USER_NOTICE);
        }
        if ($logToFile) {
            //if necessary, move the old log file and create a new one
            $this->createNewLogFile($name);
        }
        if ($logToDb) {
            //create table if necessary
            $tablename = self::tablePrefix . $name;
            $query = "CREATE TABLE IF NOT EXISTS `$tablename` (
                `$this->col_key` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `$this->col_datetime` DATETIME NOT NULL,
                `$this->col_user` INT DEFAULT NULL ,
                `$this->col_origin` VARCHAR( 255 ) NOT NULL,
                `$this->col_type` VARCHAR( 255 ) NOT NULL,
                `$this->col_info` VARCHAR( 1023 ) NOT NULL
                )";
            if (!mysql_query($query)){
                trigger_error("Logger::__construct(): Cannot create the db table '$tablename'.\nmysql error:".mysql_error(), E_USER_ERROR);
            }
        }
        $l["name"] = $name;
        $l["minLevel"] = $minLevel;
        $l["logLive"] = $logLive;
        if (is_null($logToFile))
            $logToFile = $db->getBoolOption("log_to_file", true);
        $l["logToFile"] = $logToFile;
        if (is_null($logToDb))
            $logToDb = $db->getBoolOption("log_to_db", false);
        $l["logToDb"] = $logToDb;
        $l["isDebug"] = $isDebug;
        $this->logs[] = $l;
        return true;
    }
    
    public function removeLog($name) {
        foreach ($this->logs as $k => $l)
            if ($l["name"] == $name) {
                unset($this->logs[$k]);
                return true;
            }
        return false;
    }
    
    public function getMsgCount($logName) {
        return count($this->liveEvents[$logName]);
    }
    
    public function getMessages($logName) {
        return $this->liveEvents[$logName];
    }
    
    public function getFormatted($logName) {
        $str  = "<div class=\"messagebox\" id=\"log_$logName\">\n";
        $str .= "<table summary=\"\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"messagetable\"><tbody>\n";
        if (count($this->liveEvents[$logName]) == 0) {
            $str .= "<tr><td class=\"noMessages\">(No messages)</td></tr>\n";
        } else {
            foreach ($this->liveEvents[$logName] as $msg) {
                $message = preg_replace(array('@(?<!<br />|<br>)(?:<br>|)$@im'), array("<br />"), $msg["message"]); //this line turns all simple line breaks and <br>'s into XHTML-compliant <br />'s
                $str .= "<tr>";
                $str .= "<td class=\"origin" . ((empty($msg["origin"])) ? " noOrigin" : "" ) . "\">" . $msg["origin"] . ((empty($msg["origin"])) ? "" : ":" ) . "</td>";
                $str .= "<td class=\"message\">$message</td>";
                $str .= "</tr>\n";
            }
        }
        $str .= "</tbody></table>\n";
        $str .= "</div>\n";
        return $str;
    }
    
    /**
     * Actually add a log event to all logs that fulfill the level condition.
     * (An event is logged when its level is equal to or higher than the minimum
     * level of a log).
     * @param string $origin
     * @param int $level
     * @param string $message
     * @param int $userId
     * @param array $logTo
     * @param bool[optional] $translate
     */
    private function logEvent($origin, $level, $message, $userId, $logTo, $translate = false) {
        $r = true;  //return value
        if (is_array($message))
            $translate = true;
        $when = $this->datetimeFormatter();
        global $activeUsr;
        if (defined("__USER"))
            if (isset($activeUsr) && $userId == 0)
                $userId = $activeUsr->getId();
        $msg = $message;
        if ($translate)
            $msg = Lang::translate($message, true, true);
        $msg = preg_replace(array("/<[^>]*>/i"), "", $msg);
        $CSVdata = array($when, $userId, $origin, self::$levels[$level], $msg);
        //echo "<!-- level: "; print_r($level); echo "; levels[level]: "; print_r(self::$levels[$level]); echo " -->\n";
        $DBorigin = mysql_real_escape_string($origin);
        $DBlevel = mysql_real_escape_string(self::$levels[$level]);
        $DBmessage = mysql_real_escape_string($msg);
        $query_template = "INSERT INTO `%s` (`$this->col_datetime`, `$this->col_user`, `$this->col_origin`, `$this->col_type`, `$this->col_info`)
                          VALUES ('$when', $userId, '$DBorigin', '$DBlevel', '$DBmessage')";
            
        foreach ($logTo as $index) {
            $l = $this->logs[$index];
            if ($l["logLive"]) {
                $m["level"] = $level;
                $m["origin"] = $origin;
                if ($translate)
                    $m["message"] = Lang::translate($message);
                else
                    $m["message"] = $message;
                $this->liveEvents[$l["name"]][] = $m;
            }
            if ($l["logToFile"]) {
                $r = $r && $this->appendEventToCsv($l["name"], $CSVdata);
            }
            if ($l["logToDb"]) {
                $tablename = self::tablePrefix . $l["name"];
                if (!mysql_query(sprint($query_template, $tablename))){
                    trigger_error(__METHOD__ . "(): Cannot insert log event into db table '$tablename'. MySQL error: " . mysql_error(), E_USER_WARNING);
                    $r = false;
                }
            }
        }
        
        return $r;
    }
    
	
	/**
	 * Log an event. Adds the event to all logs marked to log events whose 
	 * level is equal or lower than or equal to this one's. If the global
	 * debug flag is set, also adds the event to all debug logs that fulfill
	 * the level condition.
	 * @param string $origin
	 * @param int $level
	 * @param string $message
	 * @param int[optional] $userId
	 */
	public function log($origin, $level, $message, $userId = 0) {
        $logTo = array();
        foreach ($this->logs as $k => $v)
            if ($level >= $v["minLevel"])
                $logTo[] = $k;
        if (empty($logTo))
            return false;
        return $this->logEvent($origin, $level, $message, $userId, $logTo);
	}
	
	public function llog($origin, $level, $message, $userId = 0) {
	    $logTo = array();
        foreach ($this->logs as $k => $v)
            if ($level >= $v["minLevel"])
                $logTo[] = $k;
        if (empty($logTo))
            return false;
        return $this->logEvent($origin, $level, $message, $userId, $logTo, true);
	}
	
	/**
	 * Debug-log an event. Only adds the event to the logs if the global
	 * debug flag is set, and only adds the event to logs marked for debug
	 * logging.
	 * @param string $origin
	 * @param int $level
	 * @param string $message
	 * @param int[optional] $userId
	 */
	public function dlog($origin, $level, $message, $userId = 0) {
	    global $debug;
	    if (!$debug)
            return false;
        $logTo = array();
        foreach ($this->logs as $k => $v)
            if ($v["isDebug"] && $level >= $v["minLevel"])
                $logTo[] = $k;
        if (empty($logTo))
            return false;
        return $this->logEvent($origin, $level, $message, $userId, $logTo);
	}
	
	public function ldlog($origin, $level, $message, $userId = 0) {
        global $debug;
        if (!$debug)
            return false;
        $logTo = array();
        foreach ($this->logs as $k => $v)
            if ($v["isDebug"] && $level >= $v["minLevel"])
                $logTo[] = $k;
        if (empty($logTo))
            return false;
        return $this->logEvent($origin, $level, $message, $userId, $logTo, true);
	}

	/**
	 * search the db to find the requested log events.
	 * @param unknown_type $timebefore
	 * @param unknown_type $timeafter
	 * @param unknown_type $users
	 * @param unknown_type $origins
	 * @param unknown_type $types
	 * @param unknown_type $keywords
	 * @param unknown_type $bool_keywords
	 * @return array of search results.
	 */
	public function searchDb($basename, $timebefore, $timeafter, $users, $origins, $types, $keywords, $bool_keywords){
	    $tablename = self::tablePrefix . $basename;
		$query = "SELECT * FROM `$tablename` WHERE ";
		$items = array();
		if (!is_null($timebefore)) {
			$items[] = "$this->col_datetime < '".$this->datetimeFormatter($timebefore)."' ";
		}
		if (!is_null($timeafter)) {
			$items[] = "$this->col_datetime > '".$this->datetimeFormatter($timeafter)."' ";
		}
		if (!is_null($users)) {
			$items[] = $this->splitIntoQuery($users, " OR ", $this->col_user);
		}
		if (!is_null($origins)){
			$items[] = $this->splitIntoQuery($origins, " OR ", $this->col_origin);
		}
		if (!is_null($types)){
			$items[] = $this->splitIntoQuery($types, " OR ", $this->col_type);
		}
		if (!is_null($keywords)){
			$items[] = $this->splitIntoQuery($keywords, $bool_keywords, $this->col_info, true);
		}
		$query .= implode(" AND ", $items);
		$result_array = array(array());
		if (($result = mysql_query($query)) === false) {
			trigger_error("Logger::searchDb(): Cannot execute query to search for log events in the db.\nmysql error:".mysql_error(), E_USER_ERROR);
			$result_array = null;
		} else {
			while ($fetched = mysql_fetch_assoc($result)) {
				unset($fetched[$this->col_key]);
				$result_array[] = $fetched;
			}
		}
		return $result_array;
	}

	/**
	 * @desc Searches all the existing log files by entered criteria. Note that, if there is a
	 * large number of logs, this function might reach the memory limit of PHP and cause a fatal error.
	 * This error should be caught and somehow handled -- probably by asking the user to select a smaller
	 * time frame or increasing PHP's memory limit.
	 * @param unknown_type $timebefore
	 * @param unknown_type $timeafter
	 * @param unknown_type $users
	 * @param unknown_type $origins
	 * @param unknown_type $types
	 * @param unknown_type $keywords
	 * @param unknown_type $bool_keywords string that can be 'or' or 'and' that specifies
	 * if you want that the info field of a log event must contain respectively at least one
	 * of the specified keywords or everyone of them.
	 * @return array of arrays each one containing fields of a log event that match the user query.
	 */
	public function searchCsv($basename, $timebefore, $timeafter, $users, $origins, $types, $keywords, $bool_keywords){
		$search_result = array();
		$csv_data = $this->getAllCsvFilesContent($basename);
		
		//check every line of the complete log array against our search criteria
		foreach ($csv_data as $line_array) {
		    //note that we do not have to concatenate later checks with && because we check 
		    //  after each function-call if we still want to continue
		    $eligible = $this->checkTimeMatch($line_array[0], $timebefore, $timeafter);
            if (!is_null($users)){
                $eligible = $eligible && $this->checkUsersMatch($line_array[1], $users);
            }
            if (!is_null($origins)){
                $eligible = $eligible && $this->checkOriginsMatch($line_array[2], $origins);
            }
            if (!is_null($types)){
                $eligible = $eligible && $this->checkTypesMatch($line_array[3], $types);
            }
            if (!is_null($keywords)){
                $eligible = $eligible && $this->checkInfoMatch($line_array[4], $keywords, $bool_keywords);
            }
            
            if ($eligible){
                //append the current line/event log to the search results
                $search_result[] = $line_array;
            }
		}
		return $search_result;
	}

}


//Create global constants for the log event levels
foreach (Logger::$levels as $key => $value)
    define("LEL_" . strtoupper($value), $key);

?>