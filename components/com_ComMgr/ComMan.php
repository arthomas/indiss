<?php
/**
 * @version     2010-05-16
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

defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");
defined("__DIRAWARE") or die("Directory awareness not included [" . __FILE__ . "]");
defined("__DATABASE") or die("Database connection not included [" . __FILE__ . "]");
defined("__LANG") or die("Database connection not included [" . __FILE__ . "]");

define("__COMMAN", 1);

include_once($FULL_BASEPATH . "/includes/error_handling/LiveErrorHandler.php");
include_once($FULL_BASEPATH . "/includes/logging/Logger.php");

$handler = LiveErrorHandler::getLastHandler();
if (!$handler)
    $handler = LiveErrorHandler::add("ComMan");
    
include_once($FULL_BASEPATH . "/includes/logging/helper_loggers.php");

 
class ComMan {
    
    //---- Static properties ------------------------------------------------------------
    
    private static $commonPath = "/components";
    private static $dbTable = "components";
    public  static $components;
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $id = 0;
    private $installed = false;
    private $enabled = false;
    private $oneOfAKind = false;    //means that this component cannot be duplicated
    private $alwaysOn = false;      //means that this component cannot be disabled
    private $core = false;          //means that this component cannot be removed
    private $dname = "";            //descriptive name
    private $iname = "";            //internal name/"alias" -- may only contain alphanumerics, underscores and dashes
    private $comName = "";
    private $installedAt = 0;
    private $installedBy = 0;
    private $path = "";
    

    //---- Static methods ---------------------------------------------------------------
    
    public static function count() {
        return count(self::$components);
    }
    
    public static function getCommonPath() {
        return self::$commonPath;
    }
    
    public static function setCommonPath($path) {
        global $logDebug;
        if (!is_string($path)) {
            trigger_error($dmsg = "ComMan::setCommonPath(): first argument must be of type string", E_USER_WARNING);
            $logDebug->debuglog("Component manager", "Error", $dmsg);
            return false;
        }
        $path = "/" . trim($path, "/\\");
        self::$commonPath = $path;
        return true;
    }
    
    public static function getCom($id, $silent = false) {
        global $logError, $logDebug;
        if (!is_int($id)) {
            trigger_error($emsg = "ComMan::getCom(): first argument must be of type int", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        if (isset(self::$components[$id])) {
            $logDebug->debuglog("Component manager", "Notice", "Successfully retrieved component '" . self::$components[$id]->iname . "' by id '$id'");
            return self::$components[$id];
        } else {
            $emsg = "ComMan::getCom(): component with id '$id' was not found";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
                $logError->log("Component manager", "Error", $emsg);
            }
            return false;
        }
    }
    
    public static function getComByIname($iname, $silent = false) {
        global $logError, $logDebug;
        if (!is_string($iname)) {
            trigger_error($emsg = "ComMan::getComByName(): first argument must be of type string", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        if (!is_bool($silent)) {
            trigger_error($emsg = "ComMan::getComByName(): second argument must be of type bool", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        foreach (self::$components as $com)
            if ($com->iname == $iname) {
                $logDebug->debuglog("Component manager", "Notice", "Successfully retrieved component '" . $com->iname . "' by internal name");
                return $com;
            }
        $emsg = "ComMan::getComByName(): no component named '$iname' was found";
        if (!$silent) {
            trigger_error($emsg, E_USER_WARNING);
            $logError->log("Component manager", "Warning", $emsg);
        }
        return false;
    }
    
    public static function readDB($table) {
        global $logError, $logDebug;
        if (!is_string($table)) {
            trigger_error($emsg = "ComMan::readDB(): first argument must be of type string", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        self::$dbTable = $table;
        self::$components = array(); //reset the component array -- that way this function can also refresh the DB array
        $query = "SELECT * FROM `$table`";
        $result = mysql_query($query);
        if (!$result) {
            trigger_error($emsg = "UsrMan::readDB(): database error: " . mysql_error() . "; query: " . $query, E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        while ($row = mysql_fetch_assoc($result)) { //fetch all resulting rows
            $rows[] = $row;  //and save them into our array
        }
        if (!empty($rows)) {        //lest we "provide an illegal argument to foreach"
            foreach ($rows as $row) {
                $com = new ComMan($row["id"], $row["dname"], $row["iname"], $row["comName"], $row["installedAt"], $row["installedBy"], $row["path"], $row["enabled"], $row["oneOfAKind"], $row["alwaysOn"], $row["core"]);
                self::$components[(int)$row["id"]] = $com;
            }
        } else {
            trigger_error($emsg = "UsrMan::readDB(): The database contained no entries for components", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        $logDebug->debuglog("Component manager", "Notice", "Successfully read " . count(self::$components) . " components from database table $table");
        return true;
    }
    
    private static function generateIname($comName, $mindPath = false) {
        global $logError, $logDebug;
        if (!is_string($comName)) {
            trigger_error($emsg = "ComMan::generateIname(): first argument must be of type string", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        if (!is_bool($mindPath)) {
            trigger_error($emsg = "ComMan::generateIname(): second argument must be of type bool", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        global $FULL_BASEPATH;
        $p = $FULL_BASEPATH . self::$commonPath;
        if (!self::getComByIname($comName, true) && (!$mindPath || !file_exists($p . "/$comName")))
            return $comName;
        $i = 1;
        while (self::getComByIname($iname = sprintf("%s_%03d", $comName, $i), true) || ($mindPath && file_exists("$p/$iname")))
            $i++;
        return $iname;
    }
    
    public static function add($source, $dname = "", $iname = "", $dest = "") {
        global $logError, $logDebug, $handler;
        if (!is_string($source)) {
            trigger_error($emsg = "ComMan::add(): first argument must be of type string", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        if (!is_string($dname)) {
            trigger_error($emsg = "ComMan::add(): second argument must be of type string", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        if (!is_string($iname)) {
            trigger_error($emsg = "ComMan::add(): third argument must be of type string", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        if (!is_string($dest)) {
            trigger_error($emsg = "ComMan::add(): fourth argument must be of type string", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        
        $source = rtrim($source, "/\\");
        
        if (!file_exists($source . "/install.xml")) {
            $handler->addMsg(lang("commgrComponentManager"), lang("commgrXMLNotFound") . " ($source/install.xml)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $xml = simplexml_load_file($source . "/install.xml");
        if ( !$xml ) {
            $handler->addMsg(lang("commgrComponentManager"), lang("commgrXMLInvalid") . " ($source/install.xml)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if ((string)$xml["type"] != "component") {
            $handler->addMsg(lang("commgrComponentManager"), lang("commgrXMLNotACom") . " ($source/install.xml)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        if ( !(bool)$xml->comName || !(bool)$xml->version || !(bool)$xml->description || !(bool)$xml->files || count($xml->files->filename) < 1 ) {
            $handler->addMsg(lang("commgrComponentManager"), lang("commgrXMLNotValidComInfo") . " ($source/install.xml)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        $comName = (string)$xml->comName;
        $version = (string)$xml->version;
        $desc = (string)$xml->description;
        $files = $xml->files->filename;
        $dest = rtrim($dest, "/\\");
        if (empty($iname))
            $iname = self::generateIname($comName, empty($dest));
        if (empty($dest))
            $dest = "/$iname";
        if (empty($dname))
            $dname = ucfirst($iname);
        
        if (self::getComByIname($iname, true) !== false) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrInameAlreadyExists"), $iname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        global $FULL_BASEPATH;
        $p = $FULL_BASEPATH . self::$commonPath . $dest;
        if (file_exists($p)) {
            $handler->addMsg(lang("commgrComponentManager"), lang("commgrComDirInUse") . " ($p)", LiveErrorHandler::EK_ERROR);
            return false;
        }
        mkdir($p);
        foreach ($files as $filename) {
            if ( strpos($filename, "/") !== false || strpos($filename, "\\") !== false) {  //if the current file goes into a sub-directory
                if (!file_exists(dirname("$p/$filename"))) {                //check if this sub-directory exists
                    $r = mkdir(dirname("$p/$filename"), 0777, true);        //and create it if necessary
                    $logDebug->debuglog("Component manager", "Notice", "ComMan::add(): Recursively creating directory '" . dirname("$p/$filename") . "'... " . (($r) ? "Success" : "Fail"));
                }
            }
            $r = copy("$source/$filename", "$p/$filename");
            $logDebug->debuglog("Component manager", "Notice", "ComMan::add(): Copying file '$source/$filename' to '$p/$filename'... " . (($r) ? "Success" : "Fail"));
        }
        
        $installedAt = date($GLOBALS["datefmt"]);
        $installedBy = "NULL";
        global $activeUsr;
        if (defined("__USRMAN"))
            if (isset($activeUsr))
                $installedBy = $activeUsr->getId();
        
        $query = "INSERT INTO `" . self::$dbTable . "` (`dname`, `iname`, `comName`, `installedAt`, `installedBy`, `path`, `enabled`) 
            VALUES ('$dname', '$iname', '$comName', '$installedAt', $installedBy, '$dest', TRUE)";
        if (!mysql_query($query)) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrComInstallDBError"), $dname, "<i>".mysql_error()."</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            return false;
        }
        if (!($id = mysql_insert_id())) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrGetDBInsertIDFail"), $dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $com = new ComMan($id, $dname, $iname, $comName, $installedAt, ($installedBy == "NULL") ? null : $installedBy, $dest, true);
        self::$components[(int)$id] = $com;
        
        //$handler->addMsg("Component manager", "Component $dname successfully installed", LiveErrorHandler::EK_SUCCESS);
        return $com;
    }
    
    public static function remove(ComMan $com) {
        global $logDebug, $logError, $handler;
        if ($com->core) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrCannotRemoveCom"), $com->dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $query = "DELETE FROM `" . self::$dbTable . "` WHERE `id`=$com->id";
        if (!mysql_query($query)) {
            $me = mysql_error();
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrRemoveComDBError"), $com->dname, "<i>$me</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            $logError->log("Component manager", "Error", "ComMan::remove(): Error while removing component '$com->dname'; Database said: $me; Query: $query");
            return false;
        }
        global $FULL_BASEPATH;
        include_once ("$FULL_BASEPATH/includes/filesystem/recursiveDelete.php");
        if (recursiveDelete($FULL_BASEPATH . self::$commonPath . $com->path) === false) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrDeletingFilesFailed"), $com->dname), LiveErrorHandler::EK_ERROR);
            $logError->log("Component manager", "Error", "ComMan::remove(): Error while deleting the files of component '$com->dname'");
        }
        unset (self::$components[$com->id]);        //remove the component from the internal array
        return true;
    }
    
    public static function removeById($id) {
        global $logDebug, $logError, $handler;
        if (!is_int($id)) {
            trigger_error($emsg = "ComMan::removeById(): Argument 1 must be of type int", E_USER_ERROR);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        if (isset(self::$components[$id])) {
            trigger_error($emsg = "ComMan::removeById(): component ID '$id' was not found", E_USER_ERROR);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        return remove($com);
    }
    
    public static function removeByIname($iname) {
        global $logDebug, $logError, $handler;
        if (!is_string($iname)) {
            trigger_error("ComMan::removeByIname(): Argument 1 must be of type string", E_USER_ERROR);
            return false;
        }
        $found = false;
        foreach (self::$components as $com)
            if ($com->iname == $iname) {
                $found = true;
                break;
            }
        if (!$found) {
            trigger_error("ComMan::removeByIname(): component named '$iname' was not found", E_USER_ERROR);
            return false;
        }
        return remove($com);
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    private function __construct($id, $dname, $iname, $comName, $installedAt, $installedBy, $path, $enabled = true, $oneOfAKind = false, $alwaysOn = false, $core = false) {
        $this->id = $id;
        $this->dname = $dname;
        $this->iname = $iname;
        $this->comName = $comName;
        $this->installedAt = $installedAt;
        $this->installedBy = $installedBy;
        $this->path = $path;
        $this->enabled = $enabled;
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
        return file_exists($this->getFullPath() . "/main.php");
    }
    
    public function hasBackend() {
        return file_exists($this->getFullPath() . "/admin.php");
    }
    
    public function isOneOfAKind() {
        if (!file_exists($this->getFullPath() . "/install.xml"))
            return true;
        return $this->oneOfAKind;
    }
    
    public function isAlwaysOn() {
        return $this->alwaysOn;
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
        global $logDebug, $logError, $handler;
        if (!is_bool($enabled)) {
            trigger_error("ComMan::enable(): first argument must be of type bool", E_USER_WARNING);
            return false;
        }
        if ($this->alwaysOn) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrCannotDisableCom"), $this->dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `enabled`=" . (($enabled) ? "TRUE" : "FALSE") . " WHERE `id`='" . $this->id . "'");
        if (!$result) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang(($enabled) ? "commgrDisableComDBError" : "commgrEnableComDBError"), $this->dname, "<i>" . mysql_error() . "</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $this->enabled = $enabled;
        return true;
    }
    
    public function getDname() {
        return $this->dname;
    }
    
    public function setDname($dname) {
        global $logDebug, $logError, $handler;
        if (!is_string($enabled)) {
            trigger_error("ComMan::setName(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `name`='$dname' WHERE `id`='" . $this->id . "'");
        if (!$result) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrRenameComDBError"), $this->dname, "<i>" . mysql_error() . "</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $this->dname = $dname;
        return true;
    }
    
    public function getIname() {
        return $this->iname;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getWebPath() {
        global $basepath;
        return $basepath . self::$commonPath . $this->path;
    }
    
    public function getFullPath() {
        global $FULL_BASEPATH;
        return $FULL_BASEPATH . self::$commonPath . $this->path;
    }
    
    //Note: this function is deprecated! It will only change the path but will not move the files!
    public function setPath($path) {
        global $logDebug, $logError, $handler;
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
                $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrChangeComPathDBError"), $this->dname, "<i>" . mysql_error() . "</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
                return false;
            }
        }
        $this->path = $path;
        return true;
    }
    
    public function duplicate($dname = "", $iname = "", $dest = "") {
        global $logDebug, $logError, $handler;
        if (!is_string($dname)) {
            trigger_error("ComMan::duplicate(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!is_string($iname)) {
            trigger_error("ComMan::duplicate(): second argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!is_string($dest)) {
            trigger_error("ComMan::duplicate(): third argument must be of type string", E_USER_WARNING);
            return false;
        }
        if ($this->oneOfAKind) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrCannotDuplicateCom"), $this->dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $com = self::add($this->getFullPath(), $dname, $iname, $dest);
        /*if (!$com)
            $handler->addMsg("Component manager", "Duplicating component $this->dname failed", LiveErrorHandler::EK_ERROR);
        else
            $handler->addMsg("Component manager", "Component $this->dname was successfully duplicated to $com->dname", LiveErrorHandler::EK_SUCCESS);*/
        return $com; 
    }
    
}

?>