<?php
/**
 * @version     2010-05-03
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
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

defined("__MAIN") or die("Restricted access.");
defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");

define("__DATABASE", 1);


/**
 * 
 * @param $tablename
 * @param $name
 * @return query_result
 */
function getValueByName ($tablename, $name) {
    return getValueByNameD($tablename, $name, null);
}

/**
 * 
 * @param $tablename
 * @param $name
 * @param $default
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

function getOption($name) {
    return getValueByNameD("global_options", $name, null);
}

function getOptionD($name, $default) {
    return getValueByNameD("global_options", $name, $default);
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

class MySQLConnection {
    
    //---- Class constants --------------------------------------------------------------
    
    
    //---- Static properties ------------------------------------------------------------
    
    
    //---- Object properties ------------------------------------------------------------
    private $dbhost = "localhost";
    private $dbuser = "";
    private $dbpass = "";
    private $dbname = "";
    private $lid;                       //MySQL link identifier
    
    
    //---- Static methods ---------------------------------------------------------------
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    public function __construct ($dbhost, $dbuser, $dbpass, $dbname) {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function connect() {
        if (!($this->lid = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass))) {
            die("Error: Cannot connect to MySQL server. Server said: " . mysql_error());
        }
        if (!mysql_select_db($this->dbname)) { 
            mysql_close($this->lid);
            die("Error: Cannot select database. Server said: " . mysql_error());
        }
    }
    
    public function disconnect() {
        mysql_close($this->lid);
    }
    
    /**
     * 
     * @param $tablename
     * @param $name
     * @param $default
     * @return query_result
     */
    public function getValueByNameD ($tablename, $name, $default) {
        $result = mysql_query( "SELECT `value` FROM `$tablename` WHERE `name`='$name'", $this->lid );
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
    
    /**
     * 
     * @param $tablename
     * @param $name
     * @return query_result
     */
    public function getValueByName ($tablename, $name) {
        return $this->getValueByNameD($tablename, $name, null);
    }
    
    /**
     * 
     * @param $tablename
     * @param $id
     * @return query_result
     */
    public function getValueByID ($tablename, $id) {
        $result = mysql_query( "SELECT `value` FROM `$tablename` WHERE `id`=$id", $this->lid );
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
    
    public function getOption($name) {
        return $this->getValueByNameD("global_options", $name, null);
    }
    
    public function getOptionD($name, $default) {
        return $this->getValueByNameD("global_options", $name, $default);
    }
    
    public function getBoolOption($name) {
        $r = $this->getValueByNameD("global_options", $name, null);
        if (!is_null($r)) {
            if ($r == 0)
                return false;
            else
                return true;
        } else
            return null;
    }
    
    public function getBoolOptionD($name, $default) {
        $r = $this->getValueByNameD("global_options", $name, null);
        if (!is_null($r)) {
            if ($r == 0)
                return false;
            else
                return true;
        } else
            return $default;
    }
    
}

if (!isset($db)) {
    $db = new MySQLConnection($dbhost, $dbuser, $dbpass, $dbname);
    $db->connect();
}
	

?>