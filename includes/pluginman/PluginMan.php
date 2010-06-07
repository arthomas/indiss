<?php
/**
 * @version     2010-06-06
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

define("__PLUGINMAN", 1);

include_once($FULL_BASEPATH . "/includes/error_handling/LiveErrorHandler.php");
include_once($FULL_BASEPATH . "/includes/logging/Logger.php");

$handler = LiveErrorHandler::getLastHandler();
if (!$handler)
    $handler = LiveErrorHandler::add("PluginMan");
    
include_once($FULL_BASEPATH . "/includes/logging/helper_loggers.php");

require_once("Plugin.php");
 
class PluginMan {
    
    //---- Static properties ------------------------------------------------------------
    
    private static $commonPath = "plugins/";
    private static $dbTable = "plugins";
    public  static $plugins;
    
    //---- Static methods ---------------------------------------------------------------
    
    /**
     * Get the number of plugins currently in the internal array.
     * @return int Returns the number of plugins currently in the internal array.
     */
    public static function count() {
        return count(self::$plugins);
    }
    
    /**
     * Get the content of the $commonPath class property.
     * @return string Returns the content of the private $commonPath class property.
     */
    public static function getCommonPath() {
        return self::$commonPath;
    }
    
    /**
     * Set the content of the $commonPath class property.
     * @param string $path The new value of for the $commonPath class property.
     * @return bool At the moment, always returns true.
     */
    public static function setCommonPath($path) {
        $path = trim($path, "/\\") . "/";
        self::$commonPath = $path;
        return true;
    }
    
    /**
     * 
     * @param int $id
     * @param bool $silent
     */
    public static function getPlugin($id, $silent = false) {
        global $logError, $logDebug;
        if (isset(self::$plugins[$id])) {
            $logDebug->debuglog("Plugin manager", "Notice", "Successfully retrieved plugin '" . self::$plugins[$id]->iname . "' by id '$id'");
            return self::$plugins[$id];
        } else {
            $emsg = "PuginMan::getPlugin(): plugin with id '$id' was not found";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
                $logError->log("Plugin manager", "Error", $emsg);
            }
            return false;
        }
    }
    
    /**
     * 
     * @param string $iname
     * @param bool $silent
     */
    public static function getPluginByIname($iname, $silent = false) {
        global $logError, $logDebug;
        foreach (self::$plugins as $plugin)
            if ($plugin->iname == $iname) {
                $logDebug->debuglog("Component manager", "Notice", "Successfully retrieved component '" . $plugin->iname . "' by internal name");
                return $plugin;
            }
        $emsg = "ComMan::getComByName(): no component named '$iname' was found";
        if (!$silent) {
            trigger_error($emsg, E_USER_WARNING);
            $logError->log("Component manager", "Warning", $emsg);
        }
        return false;
    }
    
    /**
     * 
     * @param string $table
     */
    public static function readDB($table = null) {
        global $logError, $logDebug, $db;
        if (!is_null($table))
            self::$dbTable = $table;
        self::$plugins = array(); //reset the plugin array -- that way this function can also refresh the DB array
        $query = "SELECT * FROM `$table`";
        $result = $db->q($query);
        if (!$result) {
            trigger_error($emsg = "UsrMan::readDB(): database error: " . mysql_error() . "; query: " . $query, E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        while ($rows[] = mysql_fetch_assoc($result)) ; //fetch all resulting rows and save them into our array
        if (!empty($rows)) {        //lest we "provide an illegal argument to foreach"
            foreach ($rows as $row) {   //create a new object for each plugin and save it into our internal array
                $plugin = new Plugin($row["id"], $row["dname"], $row["iname"], $row["comName"], $row["installedAt"], $row["installedBy"], $row["path"], $row["enabled"], $row["oneOfAKind"], $row["alwaysOn"], $row["core"]);
                self::$plugins[(int)$row["id"]] = $plugin;
            }
        } else {
            trigger_error($emsg = "UsrMan::readDB(): The database contained no entries for components", E_USER_WARNING);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        $logDebug->debuglog("Component manager", "Notice", "Successfully read " . count(self::$plugins) . " components from database table $table");
        return true;
    }
    
    /**
     * 
     * @param unknown_type $comName
     * @param bool $mindPath If true, the function will return an iname that can also be used as a folder name (it
     * checks that that folder name is not in use)
     */
    private static function generateIname($pName, $mindPath = false) {
        global $logError, $logDebug;
        global $FBP;
        $p = $FBP . self::$commonPath;
        if (!self::getPluginByIname($pName, true) && (!$mindPath || !file_exists($p . "/$pName")))
            return $pName;
        $i = 1;
        while (self::getPluginByIname($iname = sprintf("%s_%03d", $pName, $i), true) || ($mindPath && file_exists("$p/$iname")))    //note that his line relies on the fact that the || operator is evaluated left-to-right
            $i++;
        return $iname;
    }
    
    /**
     * 
     * @param string $source
     * @param string $dname
     * @param string $iname
     * @param string $dest
     * @return mixed
     */
    public static function add($source, $dname = "", $iname = "", $dest = "") {
        global $logError, $logDebug, $handler, $db;
        
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
        $pName = (string)$xml->comName;
        $version = (string)$xml->version;
        $desc = (string)$xml->description;
        $files = $xml->files->filename;
        if (empty($iname))
            $iname = self::generateIname($comName, empty($dest));
        $dest = rtrim($dest, "/\\");
        if (empty($dest))
            $dest = "$iname";
        $dest .= "/";
        if (empty($dname))
            $dname = ucfirst($iname);
        
        if (self::getComByIname($iname, true) !== false) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrInameAlreadyExists"), $iname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        global $FBP;
        $p = $FBP . self::$commonPath . $dest;
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
        
        global $datefmt;
        if (empty($datefmt))
            $datefmt = "YmdHis";    //default this in case it's not set (e.g. when using PluginMan from the installer)
        $installedAt = date($datefmt);
        $installedBy = 0;
        global $activeUsr;
        if (defined("__USRMAN"))
            if (isset($activeUsr))
                $installedBy = $activeUsr->getId();
        
        $query = "INSERT INTO `" . self::$dbTable . "` (`dname`, `iname`, `comName`, `installedAt`, `installedBy`, `path`, `enabled`) 
            VALUES ('$dname', '$iname', '$pName', '$installedAt', $installedBy, '$dest', TRUE)";
        if (!$db->q($query)) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrComInstallDBError"), $dname, "<i>".$db->e()."</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            return false;
        }
        if (!($id = mysql_insert_id())) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrGetDBInsertIDFail"), $dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        
        $plugin = new Plugin($id, $dname, $iname, $pName, $installedAt, $installedBy, $dest, true);
        self::$plugins[(int)$id] = $plugin;
        
        //$handler->addMsg("Component manager", "Component $dname successfully installed", LiveErrorHandler::EK_SUCCESS);
        return $plugin;
    }
    
    /**
     * 
     * @param Plugin $plugin
     * @return bool Returns true on success or false on failure.
     */
    public static function remove(Plugin $plugin) {
        global $logDebug, $logError, $handler, $db;
        if ($plugin->core) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrCannotRemoveCom"), $plugin->dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $query = "DELETE FROM `" . self::$dbTable . "` WHERE `id`=$plugin->id";
        if (!$db->q($query)) {
            $me = $db->e();
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrRemoveComDBError"), $plugin->dname, "<i>$me</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            $logError->log("Component manager", "Error", "ComMan::remove(): Error while removing component '$plugin->dname'; Database said: $me; Query: $query");
            return false;
        }
        global $FBP;
        include_once ($FBP . "includes/filesystem/recursiveDelete.php");
        if (recursiveDelete( $plugin->getFullPath() ) === false) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrDeletingFilesFailed"), $plugin->dname), LiveErrorHandler::EK_ERROR);
            $logError->log("Component manager", "Error", "ComMan::remove(): Error while deleting the files of component '$plugin->dname'");
        }
        unset (self::$plugins[$plugin->id]);        //remove the component from the internal array
        return true;
    }
    
    /**
     * 
     * @param int $id
     * @return bool Returns true on success or false on failure.
     */
    public static function removeById($id) {
        global $logDebug, $logError, $handler;
        if (isset(self::$plugins[$id])) {
            trigger_error($emsg = "ComMan::removeById(): component ID '$id' was not found", E_USER_ERROR);
            $logError->log("Component manager", "Error", $emsg);
            return false;
        }
        return remove(self::$plugins[$id]);
    }
    
    /**
     * 
     * @param string $iname
     * @return bool Returns true on success or false on failure.
     */
    public static function removeByIname($iname) {
        global $logDebug, $logError, $handler;
        $found = false;
        foreach (self::$plugins as $plugins)
            if ($plugin->iname == $iname) {
                $found = true;
                break;
            }
        if (!$found) {
            trigger_error("ComMan::removeByIname(): component named '$iname' was not found", E_USER_ERROR);
            return false;
        }
        return remove($plugin);
    }
    
    
}

?>