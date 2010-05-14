<?php
/**
 * @version     2010-05-03
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
defined("__DIRAWARE") or die("Directory awareness not included [" . __FILE__ . "]");
defined("__DATABASE") or die("Database connection not included [" . __FILE__ . "]");

define("__LOGGER", 1);

class Logger {
    
    //---- Class constants --------------------------------------------------------------

	const filesize = 104857600;            //maximum file size of log file, in bytes
	const searchQueryDelimiter = ",";
	const fileNamePrefix = "indiss_";
	const fileNameDelimiter = "_";
	const fileNameSuffix = ".log";
	const tablePrefix = "logs_";
	const csvDelimiter = ";";              //cell delimiter for the CSV file; Note: changing this will make all previously created log files unreadable!
	const logsFolder = "/logs";             //directory within the INDISS folder to place the log files in; it is assumed this folder already exists
	
	
    //---- Static properties ------------------------------------------------------------

	public static $parameters = array('when', 'uid', 'origin', 'type', 'message');  //these are the values inserted below for $col_datetime, $col_user, $col_origin, $col_type and $col_info, respectively
	
	
    //---- Object properties ------------------------------------------------------------
	
	private $tablename = "logs";
	private $col_key = "id";
	private $col_datetime = null;
    private $col_user = null;
	private $col_origin = null;
	private $col_type = null;
	private $col_info = null;
	private $name;
	private $logToFile = true;
	private $logToDb = true;
	private $firstEvent = true;    //this remembers if we need to output a separator before logging the first event after creation
	private $passthrough = false;  //if true, log events will be passed through
	private $passthroughTo;        //reference(s) to other logger(s) to pass events through to
	
	
    //---- Object methods ---------------------------------------------------------------
    
	
	// Constructor:
	
    /**
     * @desc Constructor for logger objects. Each concurrently used logger should get a unique name
     * which determines db table name and file name; the name must thus be compatible with db
     * and file system naming conventions, i.e. lower-case letters, numbers and underscore only.
     * @param <i>string</i> <b>$name</b>: The name of this logger, will determine db table name and 
     * file name. Due to PHP's auto-casting feature, this can theoretically also be a number
     * (int/float) -- no promises though.
     * @return void
     */
    public function __construct($name, $logToFile = null, $logToDb = null) {
        global $db;
        if (!is_string($name)) {
            trigger_error("Logger::__construct(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!(is_bool($logToFile) || is_null($logToFile))) {
            trigger_error("Logger::__construct(): second argument must be NULL or of type bool", E_USER_WARNING);
            return false;
        }
        if (!(is_bool($logToDb) || is_null($logToDb))) {
            trigger_error("Logger::__construct(): third argument must be NULL or of type bool", E_USER_WARNING);
            return false;
        }
        if (is_null($logToFile))
            $logToFile = $db->getBoolOptionD("log_to_file", true);
        if (is_null($logToDb))
            $logToDb = $db->getBoolOptionD("log_to_db", false);
        if ( !$logToFile && !$logToDb ) {
            trigger_error("Logger::__construct(): this logger ('$name') will do nothing (log neither to file nor to DB)", E_USER_NOTICE);
        }
        $this->name = $name;
        $this->logToFile = $logToFile;
        $this->logToDb = $logToDb;
        if ($logToFile) {
            if (!file_exists($this->constructLogFileCompletePath())){
                $this->createNewLogFile();
            }
        }
        if ($logToDb) {
                $this->col_datetime = self::$parameters[0];
                $this->col_user = self::$parameters[1];
                $this->col_origin = self::$parameters[2];
                $this->col_type = self::$parameters[3];
                $this->col_info = self::$parameters[4];
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
    }
    
    
    
    
    // General utility functions:

    /**
     * @desc Formats the date passed as $time or the current time() if NULL is passed
     * to be used in a mysql query.
     * @param $time
     * @return Formatted DATETIME string
     */
    private function datetimeFormatter($time=NULL){
        if (!is_null($time)){
            $ret = date("Y-m-d H:i:s", strtotime($time));
        }else{
            $ret = date("Y-m-d H:i:s");
        }
        return $ret;
    }
    
    
    
    
    // CSV-related utility functions:

    private function constructLogFileCompletePath($index = null){
        global $FULL_BASEPATH;
        $indexbit = (is_null($index)) ? "" : self::fileNameDelimiter . sprintf("%03d", $index);
        return $FULL_BASEPATH . self::logsFolder . "/" . self::fileNamePrefix . $this->name . $indexbit . self::fileNameSuffix;
    }
    
    /**
     * @desc When the file exceeds the maximum file size, a new one is created
     * @return (bool) True on success or false on failure.
     */
    private function createNewLogFile(){
        $ret = true;

        $f = fopen($this->constructLogFileCompletePath(), "w");
        if (!$f) {
            trigger_error("Logger::createNewLogFile(): Cannot create new log file", E_USER_ERROR);
            $ret = false;
        }
        fclose($f);
        return $ret;
    }

	private function renameOldLogFile(){
		$i = 1;
		while (file_exists($fn = constructLogFileCompletePath($i))){
			$i++;
		}
		return rename($this->constructLogFileCompletePath(), $fn);
	}

	/**
	 * @desc Append a new event to the log file after checking if a new log file
	 * needs to be created becuase of file size exceeding.
	 * @param $row
	 * @return (bool)
	 */
	private function appendEventToCsv($row){
		$ret = true;
		$logfile = $this->constructLogFileCompletePath();
		if (filesize($logfile) > self::filesize){
			$this->renameOldLogFile();
			$this->createNewLogFile();
		}

		$f = fopen($logfile, "a");
		if (!$f) {
			$ret = false;
		} else {
			if (!fputcsv($f, $row, self::csvDelimiter)){
				$ret = false;
			}
		}
		fclose($f);
		return $ret;
	}

    /**
     *
     * @param <i>string</i> <b>$filename</b>: the log file's name, without the path
     * @return 2-D array, where the first dimension is the line and the second dimension is the column in the CSV file.
     */
    private function getCsvFileContent($filename){
        $ret = array();
        global $FULL_BASEPATH;
        $filename = $FULL_BASEPATH . self::logsFolder . $filename;
        if (!$f = fopen($filename, "r")) {
            trigger_error("Logger::getCsvFileContent(): Cannot open in read mode the file ($filename)", E_USER_ERROR);
            $ret = null;
        } else {
            while (($csv_line = fgetcsv($f, 1000, self::csvDelimiter)) !== FALSE) {
                $ret[] = $csv_line;
            }
        }
        fclose($f);
        return $ret;
    }

    /**
     *
     * @return One big array with all existing log files appended one after another
     */
    private function getAllCsvFilesContent(){
        $result_array = array();
        $i = 1;
        while ( file_exists($fn=sprintf("%s%s%s%03d%s", self::fileNamePrefix, $this->name, self::fileNameDelimiter, $i++, self::fileNameSuffix)) )
        {
            $result_array[] = array_merge($result_array, $this->getCsvFileContent($fn));
        }
        $result_array[] = array_merge($result_array, $this->getCsvFileContent(self::fileNamePrefix . $this->name . self::fileNameSuffix));
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
    
    
    
    
    // Database-related utility functions

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
    
    
    
    
    // Publicly relevant functions:
    
    public function passthrough($enablePassthrough, $passthroughTo = null) {
        //note: "passthrough" is abbreviated as PT in the comments here
        if (!is_bool($enablePassthrough)) {
            trigger_error("Logger::passthrough(): first argument must be of type bool", E_USER_ERROR);
            return false;
        }
        if (is_array($passthroughTo)) {
            foreach ($passthroughTo as $p) {
                if (!($p instanceof Logger)) {
                    trigger_error("Logger::passthrough(): second argument must be NULL, a Logger object or an array of Logger objects", E_USER_ERROR);
                    return false;
                }
            }
        } else if (!(is_null($passthroughTo) || $passthroughTo instanceof Logger)) {
            trigger_error("Logger::passthrough(): second argument must be NULL, a Logger object or an array of Logger objects", E_USER_ERROR);
            return false;
        }
        
        if (!is_null($passthroughTo)) {                 //if a PT target is passed on, save it; otherwise leave target unchanged
            if (!is_array($passthroughTo)) {
                $passthroughTo = array($passthroughTo);
            }
            $this->passthroughTo = $passthroughTo;
        }
        
        if (!is_null($this->passthroughTo)) {           //if there is a PT target, save the PT-enable status from the argument
            $this->passthrough = $enablePassthrough;
        } else {                                        //otherwise disable PT
            $this->passthrough = false;
        }
        
        //note: the implemented behaviour allows you to temporarily disable PT without forgetting the target
        
        return true;
    }
	
	/**
	 * @desc Log an event.
	 * @param $origin
	 * @param $type
	 * @param $message
	 * @param $userId
	 * @return (bool)
	 */
	public function log($origin, $type, $message, $userId = null, $PToverride = false) {
        $ret = true;
        if ($firstEvent) {
            $firstEvent = false;
            $this->log("Logger created", "Notice", "--------------------------------------------------------------------------------------", $userId, true);
        }
        $when = $this->datetimeFormatter();
        global $activeUsr;
        if (defined("__USRMAN"))
            if (isset($activeUsr) && is_null($userId))
                $userId = $activeUsr->getId();
        if ($this->logToFile) {
            $str = array($when, $userId, $origin, $type, $message);
            $ret = $ret && $this->appendEventToCsv($str);
        }
        if ($this->logToDb) {
            $tablename = self::tablePrefix . $this->name;
            if (!is_null($userId))
                $uid = "'$userId'";
            else
                $uid = "NULL";
            $origin = mysql_real_escape_string($origin);
            $type = mysql_real_escape_string($type);
            $message = mysql_real_escape_string($message);
            $query = "INSERT INTO `$tablename` (`$this->col_datetime`, `$this->col_user`, `$this->col_origin`, `$this->col_type`, `$this->col_info`)
                     VALUES ('$when', $uid, '$origin', '$type', '$message')";
    
            if (!mysql_query($query)){
                trigger_error("Logger::log(): Cannot insert log event into db table '$tablename'.\nmysql error: ".mysql_error(), E_USER_WARNING);
                $ret = false;
            }
        }
        if ($this->passthrough && !(is_null($this->passthroughTo)) && !$PToverride) {
            foreach ($this->passthroughTo as $pt)
                $pt->log($origin, $type, $message, $userId);
        }
        return $ret;
	}
	
	public function debuglog($origin, $type, $message, $userId = null) {
	    global $debug;
	    if ($debug) {
            if ($this->passthrough && !(is_null($this->passthroughTo))) {
                foreach ($this->passthroughTo as $pt)
                    $pt->debuglog($origin, $type, $message, $userId);
            }
            return $this->log($origin, "$type", $message, $userId, true);
	    }
        else
            return false;
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
	public function searchDb($timebefore, $timeafter, $users, $origins, $types, $keywords, $bool_keywords){
		$query = "SELECT * FROM `$this->tablename` WHERE ";
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
	public function searchCsv($timebefore, $timeafter, $users, $origins, $types, $keywords, $bool_keywords){
		$search_result = array();
		$csv_data = $this->getAllCsvFilesContent();
		
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
?>