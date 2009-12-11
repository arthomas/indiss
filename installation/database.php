<?php
/**
 * @version     2009-12-07
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Specialised database connection file for installation
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
 */

//Note: at some later point, this file (database.php) will actually be a gateway that
//      includes the approprite file depending on chosen DB type (similar to how the
//      languages work)

	defined("__INSTALL") or die("Restricted access.");
	
	// Open connection to MySQL server
    if(!mysql_connect($dbhost, $dbuser, $dbpass)) { 
        $errors[] = $log[] = ( (isset($_LANG)) ? lang("6ErrMySQLConnFailed") : "Error: Cannot connect to MySQL server; MySQL said: ") . mysql_error();
    } else {
        $log[] = (isset($_LANG)) ? lang("6LogMySQLConnSuccess") : "Connection to MySQL server successfully established";
        $dbconnected = true;
    }
    
    if ( empty( $errors ) ) {
        $db_list = mysql_list_dbs($link);
        $dbfound = false;
        for ( $i = 0; $i < mysql_num_rows($db_list); $i++ ) {
            if ( mysql_db_name($db_list, $i) == $dbname ) {
                $dbfound = true;
                break;
            }
        }
        
        if ( $dbfound ) {
            $log[] = "Database '$dbname' already exists.";
        } else {
            $log[] = "Database '$dbname' does not exist.";
            
            $result = mysql_query( "CREATE DATABASE `$dbname`" );
            if ( !$result ) {
                $errors[] = $log[] = "Error: Creating database '$dbname' failed; MySQL said: " . mysql_error;
            } else {
                $log[] = "Database '$dbname' successfully created.";
            }
        }
        
        if ( empty( $errors ) ) {
            // Open the right database
            if(!mysql_select_db($dbname)) { 
                mysql_close();
                $errors[] = $log[] = ( (isset($_LANG)) ? lang("6ErrMySQLDBSelFailed") : "Error: Cannot select database '$dbname'; MySQL said: ") . mysql_error();
            } else {
                $log[] = "Database '$dbname' successfully selected.";
            }
        }
    }
    
    
    /**
     * 
     * @param $tablename
     * @param $name
     * @return query_result
     */
    function getValueByName ($tablename, $name) {
        $result = mysql_query( "SELECT value
                                    FROM `$tablename`
                                    WHERE `name`='$name'" );
        if (!$result) {
            return false;
        } else {
            //var_dump($result); echo "\n";
            if (mysql_num_rows($result) <= 0) {
                return false;
            } else {
                $rows = mysql_fetch_assoc($result);
                return $rows["value"];
            }
        }
    }
    
    /**
     * 
     * @param $tablename
     * @param $name
     * @return query_result
     */
    function getValueByNameD ($tablename, $name, $default) {
        $result = mysql_query( "SELECT value
                                    FROM `$tablename`
                                    WHERE `name`='$name'" );
        if (!$result) {
            return $default;
        } else {
            //var_dump($result); echo "\n";
            if (mysql_num_rows($result) <= 0) {
                return $default;
            } else {
                $rows = mysql_fetch_assoc($result);
                return $rows["value"];
            }
        }
    }
    
    function makeNameValueTableQuery ( $tablename ) {
        return 
            "CREATE TABLE `$tablename` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `name` VARCHAR( 255 ) NOT NULL ,
            `value` VARCHAR( 255 ) NOT NULL ,
            `comment` VARCHAR( 255 ) NOT NULL
            )";
    }
    
    /**
     * 
     * @param $tablename
     * @param $id
     * @return query_result
     */
    function getValueByID ($tablename, $id) {
        $result = mysql_query( "SELECT value
                                    FROM `$tablename`
                                    WHERE `id`=$id" );
        if (!$result) {
            return false;
        } else {
            if (mysql_num_rows($result) <= 0) {
                return false;
            } else {
                $rows = mysql_fetch_row($result);
                return $rows[0];
            }
        }
    }
    
    function db_commit ( $query ) {
        $result = mysql_query($query);
        if ( !$result ) {
            return mysql_error();
        } else {
            return true;
        }
    }
    
    function db_commit2 ( $query, $errors, $line = 0 ) {
        $error = db_commit($query);
        if ( $error !== true ) {
            $errors[] = (($line) ? $line . ": " : "" ) . $error;
            return false;
        } else {
            return true;
        }
    }
	

?>