<?php
/**
 * @version     2010-08-05
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
 * Connection to MySQL server and according utility functions.
 * @author Patrick Lehner
 *
 */
class MySQLConnection {
    
    //---- Class constants --------------------------------------------------------------
    
    
    //---- Static properties ------------------------------------------------------------
    
    
    //---- Object properties ------------------------------------------------------------
    private $dbhost = "localhost";
    private $dbuser = "";
    private $dbpass = "";
    private $dbname = "";
    private $lid;                       //MySQL link identifier
    private $lastError;                 //remembers last error (if any; null otherwise)
    
    
    //---- Static methods ---------------------------------------------------------------
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    public function __construct ($dbhost, $dbuser, $dbpass, $dbname) {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;
    }
    
    public function __destruct () {
        $this->disconnect();    //disconnects the db server, if a connection is open
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    /**
     * Connect to the MySQL server.
     * Connects using the entered connection data and selects the entered database. Will
     * call die() upon error.
     * @return void
     */
    public function connect() {
        if (!($this->lid = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass))) {
            die("Error: Cannot connect to MySQL server. Server said: " . mysql_error($this->lid));
        }
        if (!mysql_select_db($this->dbname)) { 
            mysql_close($this->lid);
            die("Error: Cannot select database. Server said: " . mysql_error($this->lid));
        }
    }
    
    /**
     * Close the connection to the MySQL server.
     * This function is a wrapper for mysql_close() and returns its return value.
     * @return bool If a connection is open, tries to close that connection and returns
     * true on success or false on failure. If no connection is open, returns false.
     */
    public function disconnect() {
        return (is_null($this->lid)) ? false : mysql_close($this->lid);
    }
    
    /**
     * Wrapper for mysql_query()
     * @param string $query The MySQL query
     * @return mixed The return value of the query; see php manual for more info
     */
    public function q($query) {
        $r = mysql_query($query, $this->lid);
        if ($r === false)
            $this->lastError = mysql_error($this->lid);
        else 
            $this->lastError = null;
        return $r;
    }
    /**
     * Wrapper for mysql_query()
     * @param string $query The MySQL query
     * @return mixed The return value of the query; see php manual for more info
     */
    public function query($query) {
        return $this->q($query);
    }
    
    /**
     * Get the last error (if any).
     * @return mixed Returns the mysql_error() for the last query, or NULL if no
     * error occurred.
     */
    public function e() {
        return $this->lastError;
    }
    /**
     * Get the last error (if any).
     * @return mixed Returns the mysql_error() for the last query, or NULL if no
     * error occurred.
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Get the tables of the selected database as an array
     * @param string $pattern The pattern to which to compare the table names; this is a
     * MySQL simple regex; refer to MySQL docs for more info. Argument defaults to null
     * which means that the pattern part of the query is left out.
     * @param bool $silent True to suppress error messages; defaults to false.
     * @return bool Returns the list of tables in an array (may be empty) on success, or
     * boolean false on database error.
     */
    public function getTables($pattern = null, $silent = false) {
        $query = "SHOW TABLES";
        if (!is_null($pattern) && is_string($pattern))
            $query .= " LIKE '$pattern'";
        $result = $this->q($query);
        if (!$result) {
            if (!$silent)
                trigger_error(__CLASS__ . "::" . __METHOD__ . "(): database error: " . $this->e() . "; query: $query", E_USER_ERROR);
            return false;
        } else {
            $list = array();
            while ($row = mysql_fetch_row($result)) 
                $list[]=$row[0];
            return $list;
        }
    }
    
    /**
     * Get the number of tables.
     * Gets the number of tables that match a pattern (or all tables
     * if $pattern is empty). If $silent is false, the sub-call to
     * getTables() will report an occurring error via trigger_error().
     * @param string $pattern The pattern as a MySQL simple regex, or
     * NULL to list all tables of the database. Defaults to NULL.
     * @param bool $silent True to suppress error messages; defaults
     * to false.
     * @return mixed On success, the number of tables matching the
     * pattern; on failure, boolean false.
     */
    public function getTableCount($pattern = null, $silent = false) {
        $r = $this->getTables($pattern, $silent);
        if ($r === false)
            return false;
        else
            return count($r);
    }
    
    /**
     * Check if a table exists.
     * @param string $name The name of the table to check.
     * @param bool $silent True to suppress error messages; defaults to false.
     * @return bool True if the table exists, false if it doesnt or if an error
     * occurred.
     */
    public function tableExists($name, $silent = false) {
        $r = $this->getTableCount($name, $silent);
        if ($r !== false && $r > 0)
            return true;
        else
            return false;
    }
    
    /**
     * Create a name-value table.
     * This functions creates a standard general-purpose name-value table.
     * It has a primary index "id" and three varchar(255) columns: "name",
     * "value" and "comment".
     * If $silent is false, an occurring error will be case with 
     * trigger_error.
     * @param string $name The name of the table
     * @param bool $silent If true, suppresses error messages; defaults to
     * false
     * @return bool True on success, false on failure.
     */
    public function createNVTable($name, $silent = false) {
        $query = "CREATE TABLE `$name` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        if (!$this->q($query)) {
            if (!$silent)
                trigger_error(__CLASS__ . "::" . __METHOD__ . "(): database error: " . $this->e() . "; query: $query", E_USER_WARNING);
            return false;
        } else
            return true;
    }
    
    /**
     * Get the value for a name from a name-value table
     * @param string $tablename The table to read from
     * @param string $name The name of the entry to read
     * @param mixed $default The default value which is returned upon
     * error (or when there is no such db entry). Defaults to NULL.
     * @return mixed Returns the retrieved value on success, or $default
     * on failure
     */
    public function getValueByName ($tablename, $name, $default = null) {
        $result = $this->q( "SELECT `value` FROM `$tablename` WHERE `name`='$name'" );
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
     * Get the value for an ID from a name-value table
     * @param string $tablename The table to read from
     * @param int $id The entry ID to read
     * @return mixed Returns the retrieved value on success, or boolean false
     * on failure.
     */
    public function getValueByID ($tablename, $id) {
        $result = $this->q( "SELECT `value` FROM `$tablename` WHERE `id`=$id" );
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
    
    /**
     * Get a value from the 'global_options' table
     * @param string $name The name of the entry to read
     * @param mixed $default The default value which is returned upon
     * error (or when there is no such db entry). Defaults to NULL.
     * @return mixed Returns the retrieved value on success, or $default
     * on failure
     */
    public function getOption($name, $default = null) {
        return $this->getValueByName("global_options", $name, $default);
    }
    
    /**
     * Get a boolean value from the 'global_options' table.
     * This functions provides proper bool casting to shorten calls.
     * @param string $name The name of the entry to read
     * @param bool $default The default value which is returned upon
     * error (or when there is no such db entry). Defaults to false.
     * @return mixed Returns the retrieved value on success, or $default
     * on failure
     */
    public function getBoolOption($name, $default = false) {
        $r = $this->getValueByName("global_options", $name, null);
        if (!is_null($r)) {
            if ($r == 0)
                return false;
            else
                return true;
        } else
            return $default;
    }
    
    /**
     * Read all returned rows from a given result into an associative array.
     * @param resource $r The MySQL result returned by query().
     * @return mixed Returns a 2D array containing all rows. The first dimension
     * is numeric with incremental keys, the second dimension is associative with
     * the keys being the column names. Returns boolean false on error.
     */
    public function getArrayA($r) {
        if ($r === false)
            return false;
        $a = array();
        while ($row = mysql_fetch_assoc($r))
            $a[] = $row;
        return $a;
    }
    
    /**
     * Read all rows from a table.
     * @param string $tableName The name of the table to read.
     * @return mixed Returns a 2D array containing all rows. The first dimension
     * is numeric with incremental keys, the second dimension is associative with
     * the keys being the column names. Returns boolean false on error.
     */
    public function readTable($tableName) {
        return $this->getArrayA($this->q("SELECT * FROM `$tableName`"));
    }
    
    public function dropTable($name, $silent = false) {
        $query = "DROP TABLE `$name`;";
        if (!$this->q($query)) {
            if (!$silent)
                trigger_error(__CLASS__ . "::" . __METHOD__ . "(): database error: " . $this->e() . "; query: $query", E_USER_WARNING);
            return false;
        } else
            return true;
    }
    
    /**
     * A wrapper for the mysql_insert_id() function.
     * @return The ID generated for an AUTO_INCREMENT column by the previous query on success,
     * 0 if the previous query does not generate an AUTO_INCREMENT value, or FALSE if no MySQL
     * connection was established. 
     */
    public function getInsertId() {
    	return mysql_insert_id($this->lid);
    }
    
} //end of class MySQLConnection
	

?>