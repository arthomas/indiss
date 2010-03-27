<?php
/**
 * @version     2010-03-27
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
    
    const UL_UNKNOWN = 0;       //Note: these user levels could also be put into vars and thus be customizable by the admin
    const UL_UNREGISTERED = 1;
    const UL_USER = 10;
    const UL_ADMIN = 100;
    

    //---- Static properties ------------------------------------------------------------
    
    public  static $users;
    private static $dbTable = "users";
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $id = 0;            //DB entry key (id)
    private $uname = "";        //user name (login name)
    private $fullname = "";     //full user name
    private $email = "";        //user email address
    private $createdAt = 0;     //date/time of account creation
    private $createdBy = 0;     //creator of this account (uid); 0=System/undefined
    private $pass = "";         //password hash
    private $active = false;    //if this user account is currently active
    private $level = self::UL_UKNOWN;
    
    
    //---- Static methods ---------------------------------------------------------------
    
    public static function getUsr($index) {
        
    }
    
    public static function getUsrById($id) {
        
    }
    
    public static function getUsrByName($id) {
        
    }
    
    public static function readDB($table) {
        
    }
    
    public static function add() {
        
    }
    
    public static function remove(UsrMan $user) {
        
    }
    
    public static function removeByName($name) {
        
    }
    
    public static function removeById($id) {
        
    }
    
    public static function removeByIndex($index) {
        
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    private function __construct() {
        
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