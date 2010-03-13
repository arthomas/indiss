<?php
/**
 * @version     2010-03-03
 * @author      Myriam Leggieri <myriam.leggieri@gmail.com>
 * @copyright   Copyright (C) 2010 Myriam Leggieri
 * @module      Log events manager: stores log events to csv file or to Mysql db and allows
 * to perform searches over them by specify one or more values for any of their fields.
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

class Logger {

	const filesize = 100;
	const searchQueryDelimiter = ",";
	const baseName = "logEvents";
	const fileNameDelimiter = "_";
	const csvDelimiter = ";";

	public static $parameters = array('Date_and_Time', 'UserId', 'Origin', 'Type', 'Info');
	private $tablename = "logs";
	private $col_key = "id";
	private $col_user = null;
	private $col_datetime = null;
	private $col_origin = null;
	private $col_type = null;
	private $col_info = null;

	private function renameOldLogFile(){
		$i = 0;
		while (file_exists(sprintf("%s".Logger::fileNameDelimiter."%03d.log", Logger::baseName, $i))){
			$i++;
		}
		rename($this->constructLogFileCompletePath(), sprintf("%s".Logger::fileNameDelimiter."%03d.log", getcwd()."/".Logger::baseName, $i));
		return true;
	}
	/**
	 * when the file exceedes the max number of lines a new one is created
	 * @return unknown_type
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
		return getcwd()."/".Logger::baseName.".log";
	}

	/**
	 * Append a new event to the log file after checking if a new log file
	 * needs to be created becuase of file size exceeding.
	 * @param unknown_type $row
	 * @return unknown_type
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
			if (!fputcsv($f, $row, Logger::csvDelimiter)){
				$ret = false;
			}
		}
		fclose($f);
		return $ret;
	}



	/**
	 * The default constructor does nothing while if an argument is passed
	 * to it then it would be the name of the table that would include logs
	 * into the db. If this table doesnt' already exist it's created.
	 * @return unknown_type
	 */
	public function __construct() {
		$argv = func_get_args();
		switch( func_num_args() )
		{
			case 0:
				if (!file_exists($this->constructLogFileCompletePath())){
					$this->createNewLogFile();
				}
				break;
			case 1:
				$this->col_datetime = self::$parameters[0];
				$this->col_user = self::$parameters[1];
				$this->col_origin = self::$parameters[2];
				$this->col_type = self::$parameters[3];
				$this->col_info = self::$parameters[4];
				self::__construct1($argv[0]);
				break;
			default:
				break;
		}
	}


	/**
	 * Create a table for logs in the db if it doesn't exist
	 * @return unknown_type
	 */
	private function __construct1($tablename){
		$this->tablename = $tablename;

		$query = "CREATE TABLE IF NOT EXISTS `$this->tablename` (`$this->col_key` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`$this->col_datetime` DATETIME NOT NULL,
            `$this->col_user` INT NOT NULL ,
            `$this->col_origin` VARCHAR( 255 ) NOT NULL ,
            `$this->col_type` VARCHAR( 255 ) NOT NULL,
            `$this->col_info` VARCHAR( 255 ) NOT NULL
            )";
		if (!mysql_query($query)){
			trigger_error("Logger::__construct1(): Cannot create the $this->tablename table into the db.".$file."-\nmysql error:".mysql_error(), E_USER_ERROR);
		}
	}

	/**
	 * format the date passed as parameter or the current one if NULL is passed.
	 * The resulting date is in the format accepted by mysql.
	 * @param unknown_type $time
	 * @return unknown_type
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
	 * store a log event into the csv file.
	 * @param unknown_type $origin
	 * @param unknown_type $type
	 * @param unknown_type $info
	 * @param unknown_type $userId
	 * @return unknown_type
	 */
	public function logToCsv($origin, $type, $info, $userId){
		$when = $this->datetimeFormatter();
		$str = array($when, $userId, $origin, $type, $info);
		return $this->appendEventToCsv($str);
	}

	/**
	 * store a log event into the db.
	 * @param unknown_type $origin
	 * @param unknown_type $type
	 * @param unknown_type $info
	 * @param unknown_type $userId
	 * @return unknown_type
	 */
	public function logToDb($origin, $type, $info, $userId){
		$ret = true;
		$when = $this->datetimeFormatter(NULL);
		$query = "INSERT INTO `$this->tablename` (`$this->col_datetime`, `$this->col_user`, `$this->col_origin`, `$this->col_type`, `$this->col_info`)
                                    VALUES (
                                        '$when',
                                        '$userId', '$origin', '$type', '$info'
                                    )";

		if (!mysql_query($query)){
			trigger_error("Logger::logToDb(): Cannot execute the insertion of values into the db. ".$file."-\nmysql error:".mysql_error(), E_USER_ERROR);
			$ret = false;
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
	private function splitIntoQuery($toSplit, $bool=" OR ", $column, $useLikeOp=false){
		$splitted = explode(Logger::searchQueryDelimiter, $toSplit);
		$cnt = count($splitted);
		$ret = "(";
		for ($i=0; $i<$cnt ;$i++){
			if($useLikeOp){
				$ret .= " $column like '%$splitted[$i]%' ";
			}else{
				$ret .= " $column = '$splitted[$i]' ";
			}
			if ($i+1<$cnt){
				$ret .= $bool;
			}
		}
		$ret .= ") ";
		return $ret;
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
		$entered = false;
		if (!is_null($timebefore)){
			$entered = true;
			$query .= "$this->col_datetime < '".$this->datetimeFormatter($timebefore)."' ";
		}
		if (!is_null($timeafter)){
			if ($entered)$query .= " AND "; else $entered=true;
			$query .= "$this->col_datetime > '".$this->datetimeFormatter($timeafter)."' ";
		}
		if (!is_null($users)){
			if ($entered)$query .= " AND "; else $entered=true;
			$query .= $this->splitIntoQuery($users, " OR ", $this->col_user);
		}

		if (!is_null($origins)){
			if ($entered)$query .= " AND "; else $entered=true;
			$query .= $this->splitIntoQuery($origins, " OR ", $this->col_origin);
		}
		if (!is_null($types)){
			if ($entered)$query .= " AND "; else $entered=true;
			$query .= $this->splitIntoQuery($types, " OR ", $this->col_type);
		}
		if (!is_null($keywords)){
			if ($entered)$query .= " AND "; else $entered=true;
			$query .= $this->splitIntoQuery($keywords, $bool_keywords, $this->col_info, true);
		}
		$result_array = array(array());
		if (($result = mysql_query($query)) === false){
			trigger_error("Logger::searchDb(): Cannot execute query to search for log events into the db. ".$file."-\nmysql error:".mysql_error(), E_USER_ERROR);
			$result_array = null;
		}else{
			for ($ind=0; ($fetched = mysql_fetch_array($result)) ;$ind++){
				$tmp = array();
				foreach ($fetched as $key => $value){
					//the first field is omitted because it's just the auto increment key of the current record
					if (is_int($key) && $key !== 0){ 
						$tmp[$key-1] = $fetched[$key];
					}
				}
				$result_array[$ind] = $tmp;
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
		for ($i=0 ; file_exists($file=sprintf("%s".Logger::fileNameDelimiter."%03d.log", Logger::baseName, $i)); $i++)
		{
			$result_array[] = $this->getCsvFileContent($file);
		}
		$result_array[] = $this->getCsvFileContent(Logger::baseName.".log");
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