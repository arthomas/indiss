<?php
/**
 * @version     2010-03-20
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      class that holds info about installed components
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

defined("__CONFIGFILE") or die("Config file not included [ComMan.php]");
defined("__DIRAWARE") or die("Directory awareness not included [ComMan.php]");
defined("__DATABASE") or die("Database connection not included [ComMan.php]");

define("__COMMAN", 1);

include_once($FULL_BASEPATH . "/includes/error_handling/LiveErrorHandler.php");
include_once($FULL_BASEPATH . "/includes/logging/Logger.php");

$handler = LiveErrorHandler::getLastHandler();
if (!$handler)
    $handler = LiveErrorHandler::add("ComMan");
    
if (!$logError) {
    $logError = new Logger("logs_error");
}
if (!$logDebug) {
    $logDebug = new Logger("logs_debug");
}

 
class ComMan {
    
    //---- Static properties ------------------------------------------------------------
    
    private static $commonPath = "/components";
    private static $dbTable = "components";
    private static $cached = false;
    public  static $components;
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $id = 0;
    private $installed = false;
    private $enabled = false;
    private $hasFrontend = false;   //means that this component has a frontend -- important for admin-only components
    private $oneOfAKind = false;    //means that this component cannot be duplicated
    private $alwaysOn = false;      //means that this component cannot be disabled
    private $core = false;          //means that this component cannot be removed
    private $name = "";
    private $comName = "";
    private $installedAt = 0;
    private $installedBy = 0;
    private $path = "";
    

    //---- Static methods ---------------------------------------------------------------
    
    public static function count() {
        return count(self::$components);
    }
    
    public static function isCached() {
        return self::$cached;
    }
    
    public static function cached($cached) {
        if (!is_bool($cached)) {
            trigger_error($dmsg = "ComMan::cached(): first argument must be of type bool", E_USER_WARNING);
            global $logDebug;
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        self::$cached = $cached;
        return true;
    }
    
    public static function getCommonPath() {
        return self::$commonPath;
    }
    
    public static function setCommonPath($path) {
        if (!is_string($path)) {
            trigger_error($dmsg = "ComMan::setCommonPath(): first argument must be of type string", E_USER_WARNING);
            global $logDebug;
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        $path = "/" . trim($path, "/\\");
        self::$commonPath = $path;
        return true;
    }
    
    public static function getCom($index) {
        if (!is_int($index)) {
            trigger_error($dmsg = "ComMan::getCom(): first argument must be of type int", E_USER_WARNING);
            global $logDebug;
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        return self::$components[$i];
    }
    
    public static function getComById($id) {
        global $logDebug;
        if (!is_int($id)) {
            trigger_error($dmsg = "ComMan::getComById(): first argument must be of type int", E_USER_WARNING);
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        foreach (self::$components as $com)
            if ($com->id == $id)
                return $com;
        trigger_error($dmsg = "ComMan::getComById(): no component with id '$id' was found", E_USER_WARNING);
        $logDebug->debuglog("Component manager", "Warning", $dmsg);
        return false;
    }
    
    public static function getComByName($name) {
        global $logDebug;
        if (!is_string($name)) {
            trigger_error($dmsg = "ComMan::getComByName(): first argument must be of type string", E_USER_WARNING);
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        foreach (self::$components as $com)
            if ($com->name == $name)
                return $com;
        trigger_error($dmsg = "ComMan::getComByName(): no component named '$name' was found", E_USER_WARNING);
        $logDebug->debuglog("Component manager", "Warning", $dmsg);
        return false;
    }
    
    public static function readDB($table) {
        global $logDebug;
        if (!is_string($table)) {
            trigger_error($dmsg = "ComMan::readDB(): first argument must be of type string", E_USER_WARNING);
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        self::$dbTable = $table;
        $query = "SELECT * FROM `$table`";
        $result = mysql_query($query);
        if (!$result) {
            trigger_error($dmsg = "ComMan::readDB(): database error: " . mysql_error(), E_USER_WARNING);
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        while ($row = mysql_fetch_assoc($result)) { //fetch all resulting rows
            $rows[] = $row;  //and save them into our array
        }
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $com = new ComMan($row["id"], $row["name"], $row["comName"], $row["installedAt"], $row["installedBy"], $row["path"], $row["enabled"], $row["hasFrontend"], $row["oneOfAKind"], $row["alwaysOn"], $row["core"]);
                self::$components[] = $com;
            }
        }
        $logDebug->debuglog("Component manager", "Notice", "Successfully read " . count(self::$components) . " components from database table $table");
        return true;
    }
    
    private static function generatePath($comName) {
        if (!is_string($comName)) {
            trigger_error("ComMan::generatePath(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        $p = $FULL_BASEPATH . self::$commonPath;
        if (!file_exists($p . "/$comName"))
            return "$comName";
        $i = 1;
        while (file_exists(sprintf("%s/%s_%03d", $p, $comName, $i)))
            $i++;
        return sprintf("%s_%03d", $comName, $i);
    }
    
    public static function add($source, $name = null, $dest = null) {
        if (!is_string($source)) {
            trigger_error("ComMan::add(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!(is_null($name) || is_string($name))) {
            trigger_error("ComMan::add(): second argument must be NULL or of type string", E_USER_WARNING);
            return false;
        }
        if (!(is_null($dest) || is_string($dest))) {
            trigger_error("ComMan::add(): third argument must be NULL or of type string", E_USER_WARNING);
            return false;
        }
        
        global $handler;
        
        $source = rtrim($source, "/\\");
        
        $xml = simplexml_load_file($source . "/install.xml");
        if ( !$xml ) {
            $handler->addMsg("Component manager", "XML file is not valid XML ($source/install.xml)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if ((string)$xml["type"] != "component") {
            $handler->addMsg("Component manager", "This is not a component ($source/install.xml)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if ( !(bool)$xml->comName || !(bool)$xml->version || !(bool)$xml->description || !(bool)$xml->hasFrontend || !(bool)$xml->files || count($xml->files->filename) < 1 ) {
            $handler->addMsg("Component manager", "XML file is not a valid component information file ($source/install.xml)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        $comName = (string)$xml->comName;
        $version = (string)$xml->version;
        $desc = (string)$xml->description;
        $hasFrontend = (strtolower((string)$xml->hasFrontend) == "yes") ? true : false;
        $files = $xml->files->filename;
        $dest = trim($dest, "/\\");
        if (empty($dest))
            $dest = generatePath($comName);
        if (empty($name))
            $name = ucfirst($dest);
        
        if (self::getComByName($name) !== false) {
            $handler->addMsg("Component manager", "A component named '$name' already exists", LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $p = $FULL_BASEPATH . $commonPath . "/" . $dest;
        if (file_exists($p)) {
            $handler->addMsg("Component manager", "Directory already exists ($p)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        mkdir($p);
        foreach ($files as $filename) {
            if ( strpos($filename, "/") !== false || strpos($filename, "\\") !== false) {
                if (!file_exists(dirname("$p/$filename")))
                    mkdir(dirname("$p/$filename"), 0777, true);
            }
            copy("$source/$filename", "$p/$filename");
        }
        
        $installedAt = date("Ymdhis");
        
        $query = "INSERT INTO `" . self::$dbTable . "` (`name`, `comName`, `installedAt`, `installedBy`, `path`, `enabled`, `hasFrontend`) 
            VALUES ('$name', '$comName', '$installedAt', NULL, '$dest', TRUE, $hasFrontend)";
        if (!mysql_query($query)) {
            $handler->addMsg("Component manager", "Database error while installing component $name\nDatabase said: " . mysql_error() . "\nQuery: <pre>$query</pre>", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if (!($id = mysql_insert_id())) {
            $handler->addMsg("Component manager", "Could not retrieve database entry ID", LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $com = new ComMan($id, $name, $comName, $installedAt, null, $dest, true, $hasFrontend);
        self::$components[] = $com;
        
        $handler->addMsg("Component manager", "Component $name successfully installed", LiveErrorHandler::EK_SUCCESS);
        return true;
    }
    
    public static function remove(ComMan $com) {
        if ($com->core) {
            $handler->addMsg("Component manager", "Component $this->name cannot be removed", LiveErrorHandler::EK_ERROR);
            return false;
        }
    }
    
    public static function removeById($id) {
        if (!is_int($id)) {
            trigger_error("ComMan::removeById(): first argument must be of type int", E_USER_WARNING);
            return false;
        }
        $found = false;
        foreach (self::$components as $com)
            if ($com->id == $id) {
                $found = true;
                break;
            }
        if (!$found) {
            trigger_error("ComMan::removeById(): component id '$id' was not found", E_USER_WARNING);
            return false;
        }
        return remove($com);
    }
    
    public static function removeByName($name) {
        if (!is_string($name)) {
            trigger_error("ComMan::removeByName(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        $found = false;
        foreach (self::$components as $com)
            if ($com->name == $name) {
                $found = true;
                break;
            }
        if (!$found) {
            trigger_error("ComMan::removeByName(): component named '$name' was not found", E_USER_WARNING);
            return false;
        }
        return remove($com);
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    private function __construct($id, $name, $comName, $installedAt, $installedBy, $path, $enabled = true, $hasFrontend = true, $oneOfAKind = false, $alwaysOn = false, $core = false) {
        $this->id = $id;
        $this->name = $name;
        $this->comName = $comName;
        $this->installedAt = $installedAt;
        $this->installedBy = $installedBy;
        $this->path = $path;
        $this->enabled = $enabled;
        $this->hasFrontend = $hasFrontend;
        $this->oneOfAKind = $oneOfAKind;
        $this->alwaysOn = $alwaysOn;
        $this->core = $core;
        $this->installed = true;
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function isInstalled() {
        return $this->installed;
    }
    
    public function hasFrontend() {
        return $this->hasFrontend;
    }
    
    public function isOneOfAKind() {
        return $this->oneOfAKind;
    }
    
    public function isAlwaysOn() {
        return $this->alwaysOne;
    }
    
    public function isCore() {
        return $this->core;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getComName() {
        return $this->comName;
    }
    
    public function getInstalledAt() {
        return $this->installedAt;
    }
    
    public function getInstalledBy() {
        return $this->installedBy;
    }
    
    public function isEnabled() {
        return $this->enabled;
    }
    
    public function enable($enabled) {
        if (!is_bool($enabled)) {
            trigger_error("ComMan::enable(): first argument must be of type bool", E_USER_WARNING);
            return false;
        }
        if ($this->alwaysOn) {
            $handler->addMsg("Component manager", "Component $this->name cannot be disabled", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if (!$this->cached) {
            $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `enabled`=" . (($enabled) ? "TRUE" : "FALSE") . " WHERE `id`='" . $this->id . "'");
            if (!$result) {
                $handler->addMsg("Component manager", "Database error while " . (($enabled) ? "enabling" : "disabling") . " component $this->name\nDatabase said: " . mysql_error() . "\nQuery: <pre>$query</pre>", LiveErrorHandler::EK_ERROR);
                return false;
            }
        }
        $this->enabled = $enabled;
        return true;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        if (!is_string($enabled)) {
            trigger_error("ComMan::setName(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!$this->cached) {
            $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `name`='$name' WHERE `id`='" . $this->id . "'");
            if (!$result) {
                $handler->addMsg("Component manager", "Database error while renaming component $this->name\nDatabase said: " . mysql_error() . "\nQuery: <pre>$query</pre>", LiveErrorHandler::EK_ERROR);
                return false;
            }
        }
        $this->name = $name;
        return true;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    //Note: this function is deprecated! It will only change the path but will not move the files!
    public function setPath($path) {
        if (!is_string($path)) {
            trigger_error("ComMan::setPath(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if ($path[0] != '/') {
            $path = "/" . $path;
        }
        $path = rtrim($path, "/");        
        if (!$this->cached) {
            $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `path`='$path' WHERE `id`='" . $this->id . "'");
            if (!$result) {
                $handler->addMsg("Component manager", "Database error while changing path for component $this->name\nDatabase said: " . mysql_error() . "\nQuery: <pre>$query</pre>", LiveErrorHandler::EK_ERROR);
                return false;
            }
        }
        $this->path = $path;
        return true;
    }
    
    public function duplicate($name = null, $dest = null) {
        if (!(is_null($name) || is_string($name))) {
            trigger_error("ComMan::duplicate(): first argument must be NULL or of type string", E_USER_WARNING);
            return false;
        }
        if (!(is_null($dest) || is_string($dest))) {
            trigger_error("ComMan::duplicate(): second argument must be NULL or of type string", E_USER_WARNING);
            return false;
        }
        if ($this->oneOfAKind) {
            $handler->addMsg("Component manager", "Component $this->name cannot be duplicated", LiveErrorHandler::EK_ERROR);
            return false;
        }
        $com = self::add($FULL_BASEPATH . self::$commonPath . $this->path, $name, $dest);
        if (!$com)
            $handler->addMsg("Component manager", "Duplicating component $this->name failed", LiveErrorHandler::EK_ERROR);
        else
            $handler->addMsg("Component manager", "Component $this->name was successfully duplicated to $com->name", LiveErrorHandler::EK_SUCCESS);
        return $com; 
    }
    
}

?>