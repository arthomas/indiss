<?php
/**
 * @version     2009-12-11
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Specialised database file for installation (containing only utility functions)
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