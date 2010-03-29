<?php
/**
 * @version     2010-03-29
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
defined("_MAIN") or die("Language file not included [UsrMan.php]");

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
    
    public static function getUsr($id) {
        
    }
    
    public static function getUsrByUname($uname, $silent = false) {
        global $logDebug, $logError;
        if (!is_string($uname)) {
            trigger_error($emsg = "UsrMan::getUsrByUname(): first argument must be of type string", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        if (!is_bool($silent)) {
            trigger_error($emsg = "UsrMan::getUsrByUname(): second argument must be of type bool", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), lang("genError"), $emsg);
            return false;
        }
        foreach (self::$components as $com)
            if ($com->name == $name)
                return $com;
        trigger_error($dmsg = "UsrMan::getUsrByUname(): no component named '$name' was found", E_USER_WARNING);
        $logDebug->debuglog(lang("usrmanUserManager"), "Warning", $dmsg);
        return false;
    }
    
    public static function readDB($table) {
        global $logDebug, $logError;
        if (!is_string($table)) {
            trigger_error($dmsg = "UsrMan::readDB(): first argument must be of type string", E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), "Error", $dmsg);
            return false;
        }
        self::$dbTable = $table;
        $query = "SELECT * FROM `$table`";
        $result = mysql_query($query);
        if (!$result) {
            trigger_error($dmsg = "UsrMan::readDB(): database error: " . mysql_error() . "; query: " . $query, E_USER_WARNING);
            $logError->log(lang("usrmanUserManager"), "Error", $dmsg);
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
            return false;
        }
        if (!($id = mysql_insert_id())) {
            $handler->addMsg(lang("usrmanUserManager"), lang("usrmanGetDBInsertIDFail"), LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $usr = new UsrMan($id, $uname, $pass, $fullname, $email, $createdAt, $createdBy, $level, true);
        self::$users[(int)$id] = $usr;
        
        $handler->addMsg(lang("usrmanUserManager"), sprintf(lang("usrmanCreateUserSuccess"), $uname), LiveErrorHandler::EK_SUCCESS);
        return true;
    }
    
    public static function remove(UsrMan $user) {
        
    }
    
    public static function removeByName($name) {
        
    }
    
    public static function removeById($id) {
        
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
    
    public function setFullName($fullname) {
        $this->fullname = $fullname;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
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
        $this->pass = $hashedPass;
    }
    
    public function setPassAndHash($pass) {
        $this->pass = sha1($pass);
    }
    
    public function isActive() {
        return $this->active;
    }
    
    public function activate($active) {
        $this->active = $active;
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