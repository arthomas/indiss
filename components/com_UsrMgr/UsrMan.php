<?php
/**
 * @version     2010-04-10
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

defined("__CONFIGFILE") or die("Config file not included [UsrMan.php]");
defined("__DIRAWARE") or die("Directory awareness not included [UsrMan.php]");
defined("__DATABASE") or die("Database connection not included [UsrMan.php]");
defined("__LANG") or die("Language file not included [UsrMan.php]");

define("__USRMAN", 1);

include_once($FULL_BASEPATH . "/includes/error_handling/LiveErrorHandler.php");
include_once($FULL_BASEPATH . "/includes/logging/Logger.php");

$handler = LiveErrorHandler::getLastHandler();
if (!$handler)
    $handler = LiveErrorHandler::add("UsrMan");
    
if (!$logError) {
    $logError = new Logger("error");
}
if (!$logDebug) {
    $logDebug = new Logger("debug");
}
 
class UsrMan {
    
    //---- Class constants --------------------------------------------------------------
    
    const UL_UNKNOWN = 0;
    const UL_UNREGISTERED = 1;
    const UL_USER = 2;
    const UL_ADMIN = 3;
    

    //---- Static properties ------------------------------------------------------------
    
    public  static $users;
    private static $dbTable = "users";
    private static $ul_trans = array (  //user level translation table
        //Note: Always make sure that the two corresponding entries have the same value 
        self::UL_UNKNOWN => 0,          "unknown" => 0,
        self::UL_UNREGISTERED => 1,     "unregistered" => 1,
        self::UL_USER => 10,            "user" => 10,
        self::UL_ADMIN => 100,          "admin" => 100
        );
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $id = 0;            //DB entry key (id)
    private $uname = "";        //user name (login name)
    private $fullname = "";     //full user name
    private $email = "";        //user email address
    private $createdAt = 0;     //date/time of account creation
    private $createdBy = 0;     //creator of this account (uid); 0=System/undefined
    private $pass = "";         //password hash
    private $active = false;    //if this user account is currently active
    private $level = self::UL_UNKNOWN;
    
    
    //---- Static methods ---------------------------------------------------------------
    
    public static function count() {
        return count(self::$users);
    }
    
    public static function getUsr($id, $silent = false) {
        global $logDebug, $logError;
        if (!is_int($id)) {
            trigger_error($emsg = "UsrMan::getUsr(): first argument must be of type int", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (isset(self::$users[$id])) {
            $logDebug->debuglog("User manager", "Notice", "Successfully retrieved user by id '$id'");
            return self::$users[$id];
        } else {
            $emsg = "UsrMan::getUsr(): user with id '$id' was not found";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
    }
    
    public static function getUsrByUname($uname, $silent = false) {
        global $logDebug, $logError;
        if (!is_string($uname)) {
            trigger_error($emsg = "UsrMan::getUsrByUname(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (!is_bool($silent)) {
            trigger_error($emsg = "UsrMan::getUsrByUname(): second argument must be of type bool", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        foreach (self::$users as $user) {
            if ($user->uname == $uname)
                return $user;
        }
        $emsg = "UsrMan::getUsrByUname(): no user named '$uname' was found";
        if (!$silent) {
            trigger_error($emsg, E_USER_WARNING);
        }
        $logError->log("User manager", "Warning", $emsg);
        return false;
    }
    
    public static function readDB($table) {
        global $logDebug, $logError;
        if (!is_string($table)) {
            trigger_error($emsg = "UsrMan::readDB(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        self::$dbTable = $table;
        self::$users = array();                    //reset $users, so this function can also be to refresh the user data
        $query = "SELECT * FROM `$table`";
        $result = mysql_query($query);
        if (!$result) {
            trigger_error($emsg = "UsrMan::readDB(): database error: " . mysql_error() . "; query: " . $query, E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        while ($row = mysql_fetch_assoc($result)) { //fetch all resulting rows
            $rows[] = $row;  //and save them into our array
        }
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $usr = new UsrMan($row["id"], $row["uname"], $row["pass"], $row["fullname"], $row["email"], $row["createdAt"], $row["createdBy"], $row["level"], $row["active"]);
                self::$users[(int)$row["id"]] = $usr;
            }
        }
        $logDebug->debuglog("Component manager", "Notice", "Successfully read " . count(self::$users) . " components from database table $table");
        return true;
    }
    
    public static function add($uname, $fullname, $email, $level, $pass, $passIsHashed = true) {
        global $logDebug, $logError;
        if (!is_string($uname)) {
            trigger_error($emsg = "UsrMan::add(): first argument must be of type string", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        if (!is_string($fullname)) {
            trigger_error($emsg = "UsrMan::add(): second argument must be of type string", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        if (!is_string($email)) {
            trigger_error($emsg = "UsrMan::add(): third argument must be of type string", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        if (!(is_string($level) || is_int($level))) {
            trigger_error($emsg = "UsrMan::add(): fourth argument must be of type string or int", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        if (!is_string($pass)) {
            trigger_error($emsg = "UsrMan::add(): fifth argument must be of type string", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        if (!is_bool($passIsHashed)) {
            trigger_error($emsg = "UsrMan::add(): sixth argument must be of type bool", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        
        global $handler;
        
        if (self::getUsrByUname($uname, true) !== false) {
            $handler->addMsg(lang("usrmanUserManager"), sprintf(lang("usrmanUnameAlreadyExists"), $uname), LiveErrorHandler::EK_ERROR);
            $logError->log("User manager", "Error", "UsrMan::add(): A user named '$uname' already exists");
            return false;
        }
        
        $createdAt = date("Ymdhis");
        
        global $activeUsr;
        if (isset($activeUsr)) {
            $createdBy = $activeUsr->getId();
        } else {
            $createdBy = 0;
        }
        
        if (!$passIsHashed) {
            $pass = sha1($pass);
        }
        
        $level = self::$ul_trans[$level];
        
        $query = "INSERT INTO `" . self::$dbTable . "` (`uname`, `pass`, `email`, `fullname`, `createdAt`, `createdBy`, `level`, `active`) 
            VALUES ('$uname', '$pass', '$email', '$fullname', '$createdAt', $createdBy, $level, TRUE)";
        if (!mysql_query($query)) {
            $handler->addMsg(lang("usrmanUserManager"), sprintf(lang("usrmanCreateUserDBError"), $uname, mysql_error(), $query), LiveErrorHandler::EK_ERROR);
            $logError->log("User manager", "Error", "UsrMan::add(): database error: " . mysql_error() . "; query: " . $query);
            return false;
        }
        if (!($id = mysql_insert_id())) {
            $handler->addMsg(lang("usrmanUserManager"), lang("usrmanGetDBInsertIDFail"), LiveErrorHandler::EK_ERROR);
            $logError->log("User manager", "Error", "UsrMan::add(): failed to retrieve database entry id -- INTERNAL ARRAY NOW OUT OF SYNC");
            return false;
        }
        
        $usr = new UsrMan($id, $uname, $pass, $fullname, $email, $createdAt, $createdBy, $level, true);
        self::$users[(int)$id] = $usr;
        
        $handler->addMsg(lang("usrmanUserManager"), sprintf(lang("usrmanCreateUserSuccess"), $uname), LiveErrorHandler::EK_SUCCESS);
        $logDebug->debuglog("User manager", "Notice", "Successfully created user '$uname'");
        return true;
    }
    
    public static function remove(UsrMan &$user, $silent = false) {
        global $logDebug, $logError, $handler;
        if (is_null($user)) {
            trigger_error($emsg = "UsrMan::remove(): first argument cannot be NULL", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        $id = $user->getId();
        if (isset(self::$users[$id])) {
            $result = mysql_query(sprintf("DELETE FROM `%s` WHERE `id`=%d LIMIT 1", self::$dbTable, $id));
            if (!$result) {
                $emsg = "UsrMan::remove(): database error: " . mysql_error() . "; query: " . $query;
                if (!$silent) {
                    trigger_error($emsg, E_USER_WARNING);
                }
                $logError->log("User manager", "Error", $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
                return false;
            }
            $affected = mysql_affected_rows();
            if ($affected < 1) {
                $emsg = "UsrMan::remove(): Database returned no error, but said that no rows were affected during deletion of user id '$id'";
                if (!$silent) {
                    trigger_error($emsg, E_USER_WARNING);
                }
                $logError->log("User manager", "Error", $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
                return false;
            }
            unset(self::$users[$id]);
            unset($user);
            $logDebug->debuglog("User manager", "Notice", "Successfully deleted user with the id '$id'");
            return true;
        } else {
            $emsg = "UsrMan::remove(): user with id '$id' was not found in internal array";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
    }
    
    public static function removeByUname($uname, $silent = false) {
        global $logDebug, $logError;
        if (!is_string($uname)) {
            trigger_error($emsg = "UsrMan::removeByUname(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (($usr = getUsrByUname($uname, true)) !== false) {
            return self::remove($usr, true);
        } else {
            $emsg = "UsrMan::removeByUname(): user named '$uname' was not found in internal array";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
    }
    
    public static function removeById($id, $silent = false) {
        global $logDebug, $logError;
        if (!is_int($id)) {
            trigger_error($emsg = "UsrMan::removeById(): first argument must be of type int", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (isset(self::$users[$id])) {
            return self::remove(self::$users[$id], true);
        } else {
            $emsg = "UsrMan::removeById(): user with id '$id' was not found in internal array";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
    }
    
    public static function login($uname, $pass, $passIsHashed = false) {
        global $logDebug, $logError, $handler;
        if (!is_string($uname)) {
            trigger_error($emsg = "UsrMan::login(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (!is_string($pass)) {
            trigger_error($emsg = "UsrMan::login(): second argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (!is_bool($passIsHashed)) {
            trigger_error($emsg = "UsrMan::login(): third argument must be of type bool", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (empty($uname) || empty($pass)) {
            $handler->addMsg("Login", "Username or password was empty", LiveErrorHandler::EK_ERROR);
            $logError->log("User manager", "Error", "UsrMan::login(): username or password was empty");
            return false;
        }
        if (!$passIsHashed) {
            $pass = sha1($pass);
        }
        $result = mysql_query("SELECT `id`,`pass` FROM `" . self::$dbTable . "` WHERE `uname`='$uname' LIMIT 1");
        if (!$result) {
            trigger_error($emsg = "UsrMan::login(): database error: " . mysql_error() . "; -- query not included for security reasons", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        if (($row = mysql_fetch_assoc($result)) === false) {
            $handler->addMsg("Login", lang("msgWrongPWorUN"), LiveErrorHandler::EK_ERROR); //"Wrong password or username"
            $logError->log("User manager", "Error", "UsrMan::login(): Someone tried to login as unknown user '$uname' from IP " . $_SERVER['REMOTE_ADDR']);
            return false;
        }
        if ($row["pass"] != $pass) {
            $handler->addMsg("Login", lang("msgWrongPWorUN"), LiveErrorHandler::EK_ERROR); //"Wrong password or username"
            $logError->log("User manager", "Error", "UsrMan::login(): Someone tried to login as user '$uname' with a wrong password from IP " . $_SERVER['REMOTE_ADDR']);
            return false;
        }
        //login was successful
        $_SESSION['username'] = $uname;
        $_SESSION["uid"] = (int)$row["id"];
        $_SESSION['sid'] = session_id();
        $_SESSION['ip'] = $ip;
        $handler->addMsg("Login", lang("msgLoginSuccess"), LiveErrorHandler::EK_SUCCESS);
        $logDebug->debuglog("User manager", "Notice", "UsrMan::login(): User '$uname' successfully logged in");
        global $activeUsr;
        $activeUsr = self::$users[(int)$row["id"]];
        return true;
    }
    
    public static function logout() {
        global $logDebug, $logError, $handler;
        if (isset($_SESSION['uid'])) {
            
            $_SESSION = array();        //destroy server session data

            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/'); //destroy user's cookie
            }

            session_destroy();          //destroy session completely
            
            $handler->addMsg("Logout", lang("msgLogoutSuccess"), LiveErrorHandler::EK_SUCCESS);
            $logDebug->debuglog("User manager", "Notice", "UsrMan::logout(): User '$uname' successfully logged out");
        } else {
            $handler->addMsg("Logout", lang("errCantLogout"), LiveErrorHandler::EK_ERROR);
        }
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    private function __construct($id, $uname, $pass, $fullname, $email, $createdAt, $createdBy, $level, $active = true) {
        $this->id = $id;
        $this->uname = $uname;
        $this->pass = $pass;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->level = $level;
        $this->active = $active;
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
        global $logDebug, $logError;
        if (!is_string($fullname)) {
            trigger_error($emsg = "UsrMan::setFullName(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        $result = mysql_query(sprintf("UPDATE `%s` SET `fullname`='%s' WHERE `id`=%d LIMIT 1", self::$dbTable, $fullname, $this->id));
        if (!$result) {
            $emsg = "UsrMan::setFullName(): database error: " . mysql_error() . "; query: " . $query;
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
            return false;
        }
        $this->fullname = $fullname;
        return true;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        global $logDebug, $logError;
        if (!is_string($email)) {
            trigger_error($emsg = "UsrMan::setEmail(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        
        //should probably validate email first
        
        $result = mysql_query(sprintf("UPDATE `%s` SET `email`='%s' WHERE `id`=%d LIMIT 1", self::$dbTable, $email, $this->id));
        if (!$result) {
            $emsg = "UsrMan::setEmail(): database error: " . mysql_error() . "; query: " . $query;
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
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
    
    public function getPass() {
        return $this->pass;
    }
    
    public function setPassHashed($hashedPass) {
        global $logDebug, $logError;
        if (!is_string($hashedPass)) {
            trigger_error($emsg = "UsrMan::setPassHashed(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        $result = mysql_query(sprintf("UPDATE `%s` SET `pass`='%s' WHERE `id`=%d LIMIT 1", self::$dbTable, $hashedPass, $this->id));
        if (!$result) {
            $emsg = "UsrMan::setPassHashed(): database error: " . mysql_error() . "; query: " . $query;
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
            return false;
        }
        $this->pass = $hashedPass;
        return true;
    }
    
    public function setPassAndHash($pass) {
        global $logDebug, $logError;
        if (!is_string($pass)) {
            trigger_error($emsg = "UsrMan::setPassAndHash(): first argument must be of type string", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        $result = mysql_query(sprintf("UPDATE `%s` SET `pass`='%s' WHERE `id`=%d LIMIT 1", self::$dbTable, sha1($pass), $this->id));
        if (!$result) {
            $emsg = "UsrMan::setPassAndHash(): database error: " . mysql_error() . "; query: " . $query;
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
            return false;
        }
        $this->pass = sha1($pass);
        return true;
    }
    
    public function isActive() {
        return $this->active;
    }
    
    public function activate($active) {
        global $logDebug, $logError;
        if (!is_bool($active)) {
            trigger_error($emsg = "UsrMan::activate(): first argument must be of type bool", E_USER_WARNING);
            $logError->log("User manager", "Error", $emsg);
            return false;
        }
        $result = mysql_query(sprintf("UPDATE `%s` SET `active`=%s WHERE `id`=%d LIMIT 1", self::$dbTable, ($active) ? "TRUE" : "FALSE", $this->id));
        if (!$result) {
            $emsg = "UsrMan::activate(): database error: " . mysql_error() . "; query: " . $query;
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
            }
            $logError->log("User manager", "Error", $emsg . " -- INTERNAL ARRAY NOW POSSIBLY OUT OF SYNC");
            return false;
        }
        $this->active = $active;
        return true;
    }
    
    public function getLevel() {
        return $this->level;
    }
    
    public function isRegistered() {
        return ($this->level <= self::UL_UNREGISTERED);
    }
    
    public function isAdmin() {
        return ($this->level >= self::UL_ADMIN);
    }
    

}

?>