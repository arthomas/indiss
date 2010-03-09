<?php
/**
 * @version     2010-03-08
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

defined("__CONFIGFILE") or die("Config file not included [component.php]");
defined("__DIRAWARE") or die("Directory awareness not included [component.php]");
defined("__DATABASE") or die("Database connection not included [component.php]");

define("__COMPONENT", 1);

include_once($FULL_BASEPATH . "/includes/error_handling/LiveErrorHandler.php");
$handler = LiveErrorHandler::getLastHandler();
if (!$handler)
    $handler = LiveErrorHandler::add("Component");
 
class Component {
    
    //---- Static properties ------------------------------------------------------------
    
    private static $commonPath = "/components";
    private static $dbTable = "components";
    private static $cached = false;
    public  static $components = array();
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $id = 0;
    private $installed = false;
    private $enabled = false;
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
            trigger_error("Component::cached(): first argument must be of type bool", E_USER_WARNING);
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
            trigger_error("Component::setCommonPath(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if ($path[0] != '/') {
            $path = "/" . $path;
        }
        $path = rtrim($path, "/");
        self::$commonPath = $path;
        return true;
    }
    
    public static function getCom($index) {
        if (!is_int($index)) {
            trigger_error("Component::getCom(): first argument must be of type int", E_USER_WARNING);
            return false;
        }
        return self::$components[$i];
    }
    
    public static function getComById($id) {
        if (!is_int($id)) {
            trigger_error("Component::getComById(): first argument must be of type int", E_USER_WARNING);
            return false;
        }
        foreach (self::$components as $com)
            if ($com->id == $id)
                return $com;
        trigger_error("Component::getComById(): no component with id '$id' was found", E_USER_WARNING);
        return false;
    }
    
    public static function getComByName($name) {
        if (!is_string($name)) {
            trigger_error("Component::getComByName(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        foreach (self::$components as $com)
            if ($com->name == $name)
                return $com;
        trigger_error("Component::getComByName(): no component named '$name' was found", E_USER_WARNING);
        return false;
    }
    
    public static function readDB($table) {
        if (!is_string($table)) {
            trigger_error("Component::readDB(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        self::$dbTable = $table;
        $query = "SELECT * FROM `$table`";
        $result = mysql_query($query);
        if (!$result) {
            trigger_error("Component::readDB(): database error: " . mysql_error(), E_USER_WARNING);
            return false;
        }
        while ($row = mysql_fetch_assoc($result)) { //fetch all resulting rows
            $rows[] = $row;  //and save them into our array
        }
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $com = new Component($row["id"], $row["name"], $row["comName"], $row["installedAt"], $row["installedBy"], $row["path"], $row["enabled"]);
                self::$components[] = $com;
            }
        }
    }
    
    private static function generatePath($comName) {
        $p = $FULL_BASEPATH . self::$commonPath;
        if (!file_exists($p . "/$comName"))
            return "$comName";
        $i = 1;
        while (file_exists(sprintf("%s/%s_%03d", $p, $comName, $i)))
            $i++;
        return sprintf("%s_%03d", $comName, $i);
    }
    
    public static function add($xmlFile, $name = null, $path = null) {
        if (!is_string($xmlfile)) {
            trigger_error("Component::add(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!(is_null($name) || is_string($name))) {
            trigger_error("Component::add(): second argument must be NULL or of type string", E_USER_WARNING);
            return false;
        }
        if (!(is_null($path) || is_string($path))) {
            trigger_error("Component::add(): third argument must be NULL or of type string", E_USER_WARNING);
            return false;
        }
        
        global $handler;
        
        $xml = simplexml_load_file($xmlFile);
        if ( !$xml ) {
            $handler->addMsg("Component manager", "XML file is not valid XML ($xmlFile)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if ((string)$xml["type"] != "component") {
            $handler->addMsg("Component manager", "This is not a component ($xmlFile)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if ( !(bool)$xml->comName || !(bool)$xml->version || !(bool)$xml->description || !(bool)$xml->files || count($xml->files->filename) < 1 ) {
            $handler->addMsg("Component manager", "XML file is not a valid component information file ($xmlFile)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        $comName = (string)$xml->comName;
        $version = (string)$xml->version;
        $desc = (string)$xml->description;
        $files = $xml->files->filename;
        $path = ltrim($path, "/\\");
        if (empty($path))
            $path = generatePath($comName);
        if (empty($name))
            $name = ucfirst($path);
        
        if (self::getComByName($name) !== false) {
            $handler->addMsg("Component manager", "A component named '$name' already exists", LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $p = $FULL_BASEPATH . $commonPath . "/" . $path;
        if (file_exists($p)) {
            $handler->addMsg("Component manager", "Directory already exists ($p)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        mkdir($p);
        $s = dirname($xmlFile);
        foreach ($files as $filename) {
            if ( strpos($filename, "/") !== false || strpos($filename, "\\") !== false) {
                if (!file_exists(dirname("$p/$filename")))
                    mkdir(dirname("$p/$filename"), 0777, true);
            }
            copy("$s/$filename", "$p/$filename");
        }
        
        $installedAt = date("Ymdhis");
        
        $query = "INSERT INTO `" . self::$dbTable . "` (`name`, `comName`, `installedAt`, `installedBy`, `path`, `enabled`) 
            VALUES ('$name', '$comName', '$installedAt', NULL, '$path', TRUE)";
        if (!mysql_query($query)) {
            $handler->addMsg("Component manager", "Database error while installing component $name\nDatabase said: " . mysql_error() . "\nQuery: <pre>$query</pre>", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if (!($id = mysql_insert_id())) {
            $handler->addMsg("Component manager", "Could not retrieve database entry ID", LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $com = new Component($id, $name, $comName, $installedAt, null, $path, true);
        self::$components[] = $com;
        
        $handler->addMsg("Component manager", "Component $name successfully installed", LiveErrorHandler::EK_SUCCESS);
        return true;
    }
    
    public static function remove($id) {
        if (!is_int($id)) {
            trigger_error("Component::remove(): first argument must be of type int", E_USER_WARNING);
            return false;
        }
        $found = false;
        foreach (self::$components as $com)
            if ($com->getId() == $id) {
                $found = true;
                break;
            }
        if (!$found) {
            trigger_error("Component::remove(): component id '$id' was not found", E_USER_WARNING);
            return false;
        }
    }
    
    public static function removeByName($name) {
        if (!is_string($name)) {
            trigger_error("Component::removeByName(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        $found = 0;
        foreach (self::$components as $com)
            if ($com->getName() == $name)
                $found++;
        if ($found == 0) {
            trigger_error("Component::removeByName(): component named '$name' was not found", E_USER_WARNING);
            return false;
        }
        if ($found > 1) {
            trigger_error("Component::removeByName(): found $found components named '$name'", E_USER_WARNING);
            return false;
        }
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    private function __construct($id, $name, $comName, $installedAt, $installedBy, $path, $enabled) {
        $this->id = $id;
        $this->name = $name;
        $this->comName = $comName;
        $this->installedAt = $installedAt;
        $this->installedBy = $installedBy;
        $this->path = $path;
        $this->enabled = $enabled;
        $this->installed = true;
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function isInstalled() {
        return $this->installed;
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
            trigger_error("Component::enable(): first argument must be of type bool", E_USER_WARNING);
            return false;
        }
        if (!$this->cached) {
            $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `enabled`=" . (($enabled) ? "TRUE" : "FALSE") . " WHERE `id`='" . $this->id . "'");
            if (!$result) {
                trigger_error("Component::enable(): database error: " . mysql_error(), E_USER_WARNING);
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
            trigger_error("Component::setName(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!$this->cached) {
            $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `name`='$name' WHERE `id`='" . $this->id . "'");
            if (!$result) {
                trigger_error("Component::setName(): database error: " . mysql_error(), E_USER_WARNING);
                return false;
            }
        }
        $this->name = $name;
        return true;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function setPath($path) {
        if (!is_string($enabled)) {
            trigger_error("Component::setPath(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if ($path[0] != '/') {
            $path = "/" . $path;
        }
        $path = rtrim($path, "/");        
        if (!$this->cached) {
            $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `path`='$path' WHERE `id`='" . $this->id . "'");
            if (!$result) {
                trigger_error("Component::setPath(): database error: " . mysql_error(), E_USER_WARNING);
                return false;
            }
        }
        $this->path = $path;
        return true;
    }
    
    public function duplicate($name = null, $path = null) {
        
    }
    
}

?>