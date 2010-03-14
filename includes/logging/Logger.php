<?php
/**
 * @version     2010-03-14
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

defined("__CONFIGFILE") or die("Config file not included [Logger.php]");
defined("__DIRAWARE") or die("Directory awareness not included [Logger.php]");
defined("__DATABASE") or die("Database connection not included [Logger.php]");

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
	
	
    //---- Object methods ---------------------------------------------------------------

	private function renameOldLogFile(){
		$i = 1;
		while (file_exists($fn = sprintf("%s%s%s%03d%s", self::fileNamePrefix, $this->name, self::fileNameDelimiter, $i, self::fileNameSuffix))){
			$i++;
		}
		return rename($this->constructLogFileCompletePath(), $fn);
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

	private function constructLogFileCompletePath(){
	    global $FULL_BASEPATH;
		return $FULL_BASEPATH . "/logs" . self::fileNamePrefix . $this->name . self::fileNameSuffix;
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
	 * @desc Constructor for logger objects. Each concurrently used logger should get a unique name
	 * which determines db table name and file name; the name must thus be compatible with db
	 * and file system naming conventions, i.e. lower-case letters, numbers and underscore only.
	 * @param <i>string</i> <b>$name</b>: The name of this logger, will determine db table name and 
	 * file name. Due to PHP's auto-casting feature, this can theoretically also be a number
	 * (int/float) -- no promises though.
	 * @return void
	 */
	public function __construct($name, $logToFile = true, $logToDb = true) {
        if (!is_string($name)) {
            trigger_error("Logger::__construct(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!is_bool($logToFile)) {
            trigger_error("Logger::__construct(): second argument must be of type bool", E_USER_WARNING);
            return false;
        }
        if (!is_bool($logToDb)) {
            trigger_error("Logger::__construct(): third argument must be of type bool", E_USER_WARNING);
            return false;
        }
        if ( !$logToFile && !$logToDb ) {
            trigger_error("Logger::__construct(): this logger will do nothing (log neither to file nor to DB)", E_USER_WARNING);
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
	
	/**
	 * @desc Log an event.
	 * @param $origin
	 * @param $type
	 * @param $message
	 * @param $userId
	 * @return (bool)
	 */
	public function log($origin, $type, $message, $userId = 0) {
        $ret = true;
        $when = $this->datetimeFormatter();
        if ($this->logToFile) {
            $str = array($when, $userId, $origin, $type, $info);
            $ret = $ret && $this->appendEventToCsv($str);
        }
        if ($this->logToDb) {
            $tablename = self::tablePrefix . $this->name;
            if ($userId)
                $uid = "'$userId'";
            else
                $uid = "NULL";
            $query = "INSERT INTO `$tablename` (`$this->col_datetime`, `$this->col_user`, `$this->col_origin`, `$this->col_type`, `$this->col_info`)
                     VALUES ('$when', $uid, '$origin', '$type', '$info')";
    
            if (!mysql_query($query)){
                trigger_error("Logger::log(): Cannot insert log event into db table '$tablename'.\nmysql error:".mysql_error(), E_USER_WARNING);
                $ret = false;
            }
        }
        return $ret;
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
	 * search all the saved log files to find the requested log events.
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
		//for each file
		for ($ind=0; $ind<count($csv_data); $ind++){
			$file_array = $csv_data[$ind];
			//for each line of the current file
			for ($ind_line=0; $ind_line<count($file_array) ;$ind_line++){
				$eligible = true; //each line is potentially eligible initially
				$line_array = $file_array[$ind_line];
				//for each field of the current line
				for ($ind_field=0; $ind_field<count($line_array) ;$ind_field++){
					if ($eligible){
						switch($ind_field) {
							case 0: //when
								$eligible = $this->checkTimeMatch($line_array[$ind_field], $timebefore, $timeafter);
								break;
							case 1: //users
								if (!is_null($users)){
									$eligible = $this->checkUsersMatch($line_array[$ind_field], $users);
								}
								break;
							case 2: //origins
								if (!is_null($origins)){
									$eligible = $this->checkOriginsMatch($line_array[$ind_field], $origins);
								}
								break;
							case 3: //types
								if (!is_null($types)){
									$eligible = $this->checkTypesMatch($line_array[$ind_field], $types);
								}
								break;
							case 4: //info
								if (!is_null($keywords)){
									$eligible = $this->checkInfoMatch($line_array[$ind_field], $keywords, $bool_keywords);
								}
								break;
						}
					}
				} //all the fields of the current line have been processed
				if ($eligible){
					//append the current line/event log to the search results
					$search_result[] = $line_array;
				}
			}
		}
		return $search_result;
	}

	/**
	 *
	 * @return array that contains an entry for each file, each one being an
	 * array that contains an entry for each line of the file
	 */
	private function getAllCsvFilesContent(){
		$result_array = array();
		for ($i=0 ; file_exists($file=sprintf("%s".Logger::fileNameDelimiter."%03d.log", Logger::prefix, $i)); $i++)
		{
			$result_array[] = $this->getCsvFileContent($file);
		}
		$result_array[] = $this->getCsvFileContent(Logger::prefix.".log");
		return $result_array;
	}

	/**
	 *
	 * @param unknown_type $file
	 * @return array of arrays each one containing info from one of the lines of the specified file.
	 */
	private function getCsvFileContent($file){
		$ret = array();
		if (!$f = fopen($file, "r")) {
			trigger_error("Logger::getCsvFileContent(): Cannot open in read mode the file ".$file, E_USER_ERROR);
			$ret = null;
		} else {
			while (($csv_line = fgetcsv($f, 1000, Logger::csvDelimiter)) !== FALSE) {
				$ret[] = $csv_line;
			}
		}
		fclose($f);
		return $ret;
		//$row = 1;
		//if (($handle = fopen($file, "r")) !== FALSE) {
		//    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		//        $num = count($data);
		//        echo "<p> $num fields in line $row: <br /></p>\n";
		//        $row++;
		//        for ($c=0; $c < $num; $c++) {
		//            echo $data[$c] . "<br />\n";
		//        }
		//    }
		//    fclose($handle);
		//}

	}

}
?>