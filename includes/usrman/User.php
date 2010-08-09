<?php
/**
 * @version     2010-08-07
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
 * @module      User manager core component
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

defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");
defined("__DATABASE") or die("Database connection not included [" . __FILE__ . "]");
defined("__LANG") or die("Language file not included [" . __FILE__ . "]");

define("__USRMAN", 1);

//user level INDEX constants
define("UL_UNKNOWN", 0);
define("UL_UNREGISTERED", 1);
define("UL_USER", 2);
define("UL_ADMIN", 3);

/**
 * Class to manage user accounts internally.
 * @author Patrick Lehner
 *
 */
class User {
    
    //---- Class constants --------------------------------------------------------------
    const HASH_ALG = "sha1";  //hash algorithm to use for pasword hashing
    const MIN_PW_LENGTH = 8;  //minimum password length


    //---- Static properties ------------------------------------------------------------
    
    public  static $users;
    private static $dbTable     = "users";
    private static $ul_trans    = array (  //user level translation table
        //Note: Always make sure that the two corresponding entries have the SAME VALUE!
        UL_UNKNOWN => 0,          "unknown" => 0,
        UL_UNREGISTERED => 1,     "unregistered" => 1,
        UL_USER => 10,            "user" => 10,
        UL_ADMIN => 100,          "admin" => 100
        );
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $id             = 0;                //DB entry key (id)
    private $uname          = "";               //user name (login name)
    private $fullname       = "";               //full user name
    private $email          = "";               //user email address
    private $createdAt      = 0;                //date/time of account creation
    private $createdBy      = 0;                //creator of this account (uid); 0=System, null=unknown
    private $pass           = "";               //password hash
    private $salt           = 0;                //password salt - i currently have no compunctions about holding the salt in memory because that memory is on the server and thus usually inaccessible from the client's side
    private $active         = false;            //if this user account is currently active
    private $level          = 0;
    
    
    //---- Static methods ---------------------------------------------------------------
    
    /**
     * Get the number of user accounts currently in the array.
     * @return int Returns the count of items in the internal user account array.
     */
    public static function count() {
        return count(self::$users);
    }
    
    private static function generateSalt($length = 16) {
        $out = "";
        for ($i = 0; $i < $length; $i++) {
            $out .= chr(mt_rand(32, 254));
        }
        return $out;
    }
    
    /**
     * Get a user account by its ID and handle possible errors.
     * @param int $id The ID of the user account.
     * @param bool $silent If true, suppresses error output; defaults to false.
     * @return mixed Returns the user account object on success, or boolean false on error.
     */
    public static function getUser($id, $silent = false) {
        global $log;
        if (isset(self::$users[$id])) {
            $log->dlog("User manager", LEL_NOTICE, __METHOD__ . "(): Successfully retrieved user by id '$id'");
            return self::$users[$id];
        } else {
            $emsg = __METHOD__ . "(): user with id '$id' was not found";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
                $log->log("User manager", LEL_ERROR, $emsg);
            } else {
                $log->dlog("User manager", LEL_ERROR, $emsg);
            }
            return false;
        }
    }
    
    /**
     * Get a user account by its user name and handle possible errors.
     * @param string $uname The user name of the account.
     * @param bool $silent If true, suppresses error output; defaults to false.
     * @return mixed Returns the user account object on success, or boolean false on error.
     */
    public static function getUserByUname($uname, $silent = false) {
        global $log;
        foreach (self::$users as $user) {
            if ($user->uname == $uname) {
                $log->dlog("User manager", LEL_NOTICE, __METHOD__ . "(): Successfully retrieved user by uname '$uname'");
                return $user;
            }
        }
        $emsg = __METHOD__ . "(): no user named '$uname' was found";
        if (!$silent) {
            trigger_error($emsg, E_USER_WARNING);
            $log->log("User manager", LEL_ERROR, $emsg);
        } else {
            $log->dlog("User manager", LEL_ERROR, $emsg);
        }
        return false;
    }
    
    /**
     * Read the database table into the internal array.
     * @param string $table The name of the table to read from (default: 'users').
     * @return bool Returns true on success, or false on error.
     */
    public static function readDB() {
        global $log, $db;
        $table = self::$dbTable;
        self::$users = array();                    //reset $users, so this function can also be to refresh the user data
        
        if (($rows = $db->readTable($table)) === false) {
            $log->log("User manager", LEL_ERROR, __METHOD__ . "(): Database error while read user table: " . $db->e());
            return false;
        }
        
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $usr = new User($row);
                self::$users[(int)$row["id"]] = $usr;
            }
        }
        
        $log->dlog("User manager", LEL_NOTICE, __METHOD__ . "(): Successfully read " . count(self::$users) . " users from database table $table");
        return true;
    }
    
    public static function add($uname, $fullname, $email, $level, $pass) {
        global $log, $db;
        
        if (self::getUsrByUname($uname, true) !== false) {
            $log->log("User manager", LEL_ERROR, __METHOD__ . "(): A user named '$uname' already exists");
            return false;
        }
        
        $createdAt = date($GLOBALS["datefmt"]);
        
        global $activeUsr;
        if (isset($activeUsr)) {
            $createdBy = $activeUsr->getId();
        } else {
            $createdBy = 0;
        }
        
        $salt = self::generateSalt();
        $pass = hash(self::HASH_ALG, $salt . $pass);
        
        $level = self::$ul_trans[$level];
        
        $query = "INSERT INTO `" . self::$dbTable . "` (`uname`, `pass`, `salt`, `email`, `fullname`, `createdAt`, `createdBy`, `level`, `active`) 
            VALUES ('$uname', '$pass', '$salt', '$email', '$fullname', '$createdAt', $createdBy, $level, TRUE)";
        if (!$db->q($query)) {
            $log->log("User manager", LEL_ERROR, __METHOD__ . "(): database error: {$db->e()}; query: $query");
            return false;
        }
        if (($id = $db->getInsertId()) === false) {
            $log->log("User manager", "Error", __METHOD__ . "(): failed to retrieve database entry id");
            $log->dlog("User manager", "Error", __METHOD__ . "(): failed to retrieve database entry id -- INTERNAL ARRAY NOW OUT OF SYNC");
            return false;
        }
        
        if (!($rows = $db->getArrayA($db->q("SELECT * FROM `" . self::$dbTable . "` WHERE `id`=$id")))) {
            $log->log("User manager", LEL_ERROR, __METHOD__ . "(): database error: {$db->e()}; query: $query");
            return false;
        }
        
        $usr = new User($rows[0]);
        self::$users[(int)$id] = $usr;
        
        $log->dlog("User manager", LEL_NOTICE, __METHOD__ . "(): Successfully created user '$uname'");
        return true;
    }
    
    public static function remove($id, $silent = false) {
        global $log, $db;
        $id = (int)$id;
        if (isset(self::$users[$id])) {
            
            //try to delete user from database
            $query = "DELETE FROM `" . self::$dbTable . "` WHERE `id`=$id";
            if (!$db->q($query)) {
                $emsg = __METHOD__ . "(): database error: {$db->e()}; query: $query";
                if (!$silent) {
                    trigger_error($emsg, E_USER_WARNING);
                    $log->log("User manager", LEL_ERROR, $emsg);
                }
                $log->dlog("User manager", LEL_ERROR, $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
                return false;
            }
            
            //check how many rows were affected
            if(($affected = $db->getAffectedRows()) != 1) {
                $emsg = __METHOD__ . "(): Database returned no error, but said that $affected rows were affected during deletion of user id '$id'";
                if (!$silent) {
                    trigger_error($emsg, E_USER_WARNING);
                    $log->log("User manager", LEL_ERROR, $emsg);
                }
                $log->dlog("User manager", LEL_ERROR, $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
                return false;
            }
            
            //remove user from internal array
            unset(self::$users[$id]);
            
            //debuglog success and return
            $log->dlog("User manager", LEL_NOTICE, __METHOD__ . "(): Successfully deleted user with the id '$id'");
            return true;
            
        } else {
            $emsg = __METHOD__ . "(): user with id '$id' was not found in internal array";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
                $log->log("User manager", LEL_ERROR, $emsg);
            } else
                $log->dlog("User manager", LEL_ERROR, $emsg);
            return false;
        }
    }
    
    public static function login($uname, $pass, $silent = false) {
        global $log, $db;
        
        $pass = hash(self::HASH_ALG, $salt . $pass);
        
        $r = $db->getArrayA($db->q($qry = "SELECT `id`,`pass`,`salt` FROM `" . self::$dbTable . "` WHERE `uname`='$uname' LIMIT 1"));
        
        if ($r === false) {  //an actual error when communicating with the db server occurred
            //trigger_error($emsg = "UsrMan::login(): database error: " . mysql_error() . "; -- query not included for security reasons", E_USER_WARNING);
            $log->log("User manager", LEL_ERROR, __METHOD__ . "(): database error: {$db->e()}; query: $qry");
            return false;
        }
        
        if (count($r) < 1) { //if no such uname was found in the db
            if (!$silent) {
                $log->log("User manager", LEL_ERROR, "Login failed: Wrong username or password.");
            }
            $log->dlog("User manager", LEL_ERROR, __METHOD__ . "(): Someone tried to login as unknown user '$uname' from IP {$_SERVER['REMOTE_ADDR']}");
            return false;
        }
        
        $r = $r[0];
        
        $pass = hash(self::HASH_ALG, $r["salt"] . $pass);
        
        if ($r["pass"] != $pass) { //if the password does not match
            if (!$silent) {
                $log->log("User manager", LEL_ERROR, "Login failed: Wrong username or password.");
            }
            $log->dlog("User manager", LEL_ERROR, __METHOD__ . "(): Someone tried to login as user '$uname' with a wrong password from IP {$_SERVER['REMOTE_ADDR']}");
            return false;
        }
        
        //login was successful
        $_SESSION["uname"] = $uname;
        $_SESSION["uid"] = (int)$r["id"];
        $_SESSION["sid"] = session_id();
        $_SESSION["ip"] = $ip;
        $log->log("User manager", LEL_NOTICE, "Login successful.");
        $log->dlog("User manager", LEL_NOTICE, __METHOD__ . "(): User '$uname' successfully logged in");
        
        global $activeUsr;
        $activeUsr = self::$users[(int)$r["id"]];
        return true;
    }
    
    public static function logout() {
        global $log;
        
        if (isset($_SESSION['uid'])) {
            
            $_SESSION = array();        //destroy server session data

            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/'); //destroy user's cookie
            }

            session_destroy();          //destroy session completely
            
            $log->llog("Name_PluginManager", LEL_NOTICE, "Core_PluginMan_LogoutSuccess");
            $log->dlog("User manager", LEL_NOTICE, __METHOD__ . "(): User '$uname' successfully logged out");
        } else {
            $log->llog("Name_PluginManager", LEL_ERROR, "Core_PluginMan_CantLogout");
        }
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    private function __construct($dbRow) {
        $this->id = (int)$dbRow["id"];
        $this->uname = $dbRow["uname"];
        $this->fullname = $dbRow["fullname"];
        $this->email = $dbRow["email"];
        $this->createdAt = $dbRow["createdAt"];
        $this->createdBy = $dbRow["createdBy"];
        $this->level = (int)$dbRow["level"];
        $this->active = ($dbRow["active"] == 1) ? true : false;
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function getId() {
        return $this->id;
    }
    
    public function getUname() {
        return $this->uname;
    }
    
    public function getFullName() {
        return $this->fullname;
    }
    
    public function setFullName($fullname, $silent = false) {
        global $log, $db;
        
        if (!$db->q($qry = sprintf("UPDATE `%s` SET `fullname`='%s' WHERE `id`=%d LIMIT 1", self::$dbTable, $fullname, $this->id))) {
            $emsg = __METHOD__ . "(): database error: {$db->q()}; query: $qry";
            if (!$silent) {
                //trigger_error($emsg, E_USER_WARNING);
                $log->log("User manager", LEL_ERROR, $emsg);
            } else {
                $log->dlog("User manager", LEL_ERROR, $emsg);
            }
            return false;
        }
        $this->fullname = $fullname;
        return true;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email, $silent = false) {
        global $log, $db;
        
        if (!$db->q($qry = sprintf("UPDATE `%s` SET `email`='%s' WHERE `id`=%d LIMIT 1", self::$dbTable, $email, $this->id))) {
            $emsg = __METHOD__ . "(): database error: {$db->q()}; query: $qry";
            if (!$silent) 
                $log->log("User manager", LEL_ERROR, $emsg);
            else
                $log->dlog("User manager", LEL_ERROR, $emsg);
            return false;
        }
        $this->email = $email;
        return true;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getCreatedBy() {
        return $this->createdBy;
    }
    
    public function setPass($pass, $silent = false) {
        global $log, $db;
        
        if (strlen($pass) < self::MIN_PW_LENGTH) {
            $emsg = __METHOD__ . "(): password is too short. Minimum password length: " . self::MIN_PW_LENGTH;
            if (!$silent)
                $log->log("User manager", LEL_ERROR, $emsg);
            else
                $log->dlog("User manager", LEL_ERROR, $emsg);
            return false;
        }
        
        $salt = self::generateSalt();
        $pass = hash(self::HASH_ALG, $salt . $pass);
        
        if (!$db->q(sprintf("UPDATE `%s` SET `salt`='%s',`pass`='%s' WHERE `id`=%d LIMIT 1", self::$dbTable, $salt, $pass, $this->id))) {
            $emsg = __METHOD__ . "(): database error: {$db->q()}; query left out for security reasons";
            if (!$silent) 
                $log->log("User manager", LEL_ERROR, $emsg);
            else
                $log->dlog("User manager", LEL_ERROR, $emsg);
            return false;
        }
        
        return true;
    }
    
    public function isActive() {
        return $this->active;
    }
    
    public function setActive($active) {
        global $log, $db;
        
        
        if (!$db->q($qry = sprintf("UPDATE `%s` SET `active`=%s WHERE `id`=%d LIMIT 1", self::$dbTable, ($active) ? "TRUE" : "FALSE", $this->id))) {
            $emsg = __METHOD__ . "(): database error: {$db->q()}; query: $qry";
            if (!$silent) 
                $log->log("User manager", LEL_ERROR, $emsg);
            else
                $log->dlog("User manager", LEL_ERROR, $emsg);
            return false;
        }
        
        $this->active = $active;
        return true;
    }
    
    public function getLevel() {
        return $this->level;
    }
    
    public function isRegistered() {
        return ($this->level >= UL_UNREGISTERED);
    }
    
    public function isAdmin() {
        return ($this->level >= UL_ADMIN);
    }
    

}

?>