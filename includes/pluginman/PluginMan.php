<?php
/**
 * @version     2010-09-16
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      class that manages installed plugins
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
defined("__DATABASE") or die("Database connection not included [" . __FILE__ . "]");
defined("__LANG") or die("Database connection not included [" . __FILE__ . "]");

define("__PLUGINMAN", 1);

require_once("Plugin.php");

/**
 * Class to manage Plugins internally and keep them in sync with the database.
 * @author Patrick Lehner
 *
 */
class PluginMan {
    
    //---- Static properties ------------------------------------------------------------
    
    private static $commonPath = "plugins/";
    //private static $dataPath = "pluginData/";
    private static $pluginTable = "plugins";
    private static $pluginInfoTable = "plugin_info";
    private static $pluginInfo = array();
    private static $pluginInstanceInfo = array();
    private static $pluginObjects = array();
    
    //---- Static methods ---------------------------------------------------------------
    
    /**
     * Get the number of plugins currently in the info array.
     * @return int Returns the number of plugins currently in the info array.
     */
    public static function count() {
        return count(self::$pluginInfo);
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
        global $log;
        $path = trim($path, "/\\") . "/";
        self::$commonPath = $path;
        $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Changed common path to $path");
        return true;
    }
    
    public static function getInfoArrays(&$pluginInfo, &$pluginInstanceInfo) {
        $pluginInfo = self::$pluginInfo;
        $pluginInstanceInfo = self::$pluginInstanceInfo;
    }
    
    public static function getObjectArray() {
        return self::$pluginObjects;
    }
    
    private static function loadPlugin($id) {
        global $log;
        if (!isset(self::$pluginObjects[$id])) {
            global $FBP;
            $pluginClass = "Plugin" . self::$pluginInstanceInfo[$id]["pName"];
            if (!class_exists($pluginClass)) {
                include_once($s = $FBP . self::$commonPath . self::$pluginInstanceInfo[$id]["pName"] . "/$pluginClass.class.php");
                if (file_exists($l = (dirname($s) . "/lang"))) {
                    global $defaultlang, $lang;
                    if (file_exists("$l/$defaultlang"))
                        Lang::readLangFilesFromDir("$l/$defaultlang", true);
                    if ($lang != $defaultlang && file_exists("$l/$lang"))
                        Lang::readLangFilesFromDir("$l/$lang");
                }
                $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Loaded Plugin class file $s");
            }
            self::$pluginObjects[$id] = new $pluginClass(self::$pluginInstanceInfo[$id], self::$pluginInfo[self::$pluginInstanceInfo[$id]["pName"]]);
            $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Created new plugin object of class $pluginClass");
        }
        if (!self::$pluginObjects[$id]->isInitialized()) {
            self::$pluginObjects[$id]->initialize();
            $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Initialized Plugin '" . self::$pluginInstanceInfo[$id]["iname"] . "'");
        }
        $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Successfully loaded Plugin '" . self::$pluginInstanceInfo[$id]["iname"] . "'");
        return self::$pluginObjects[$id];
    }
    
    /**
     * Retrieve a plugin by its ID.
     * @param int $id
     * @param bool[optional] $silent
     * @return mixed Returns the Plugin object on success or boolean false on failure.
     */
    public static function getPlugin($id, $silent = false) {
        global $log;
        if (isset(self::$pluginInstanceInfo[$id])) {
            //debug-log events are located in loadPlugin()
            return self::loadPlugin($id);
        } else {
            $emsg = __METHOD__ . "(): Plugin with id '$id' was not found";
            if (!$silent) {
                trigger_error($emsg, E_USER_WARNING);
                $log->log("Plugin manager", LEL_ERROR, $emsg);
            } else {
                $log->dlog("Plugin manager", LEL_NOTICE, $emsg . " (silent mode)");
            }
            return false;
        }
    }
    
    /**
     * Retrieve a plugin by its iname.
     * @param string $iname
     * @param bool[optional] $silent
     * @return mixed Returns the Plugin object on success or boolean false on failure.
     */
    public static function getPluginByIname($iname, $silent = false) {
        global $log;
        foreach (self::$pluginInstanceInfo as $pI)
            if ($pI["iname"] == $iname) {
                //debug-log events are located in loadPlugin()
                return self::loadPlugin($pI["id"]);
            }
        $emsg = __METHOD__ . "(): no plugin named '$iname' was found";
        if (!$silent) {
            $log->log("Plugin manager", LEL_WARNING, $emsg);
            trigger_error($emsg, E_USER_WARNING);
        } else {
            $log->dlog("Plugin manager", LEL_NOTICE, $emsg . " (silent mode)");
        }
        return false;
    }
    
    /**
     * Read all plugins from the database into the internal array.
     * @param string[optional] $table
     * @return bool Returns true on success or false on failure.
     */
    public static function readDB() {
        global $log, $db;
        $pTable = self::$pluginTable;
        $pInfoTable = self::$pluginInfoTable;
        
        if (($t = $db->readTable(self::$pluginInfoTable)) === false) {
            $log->log("Plugin manager", LEL_ERROR, $emsg = __METHOD__ . "(): database error: " . $db->e());
            trigger_error($emsg, E_USER_ERROR);
            return false;
        }
        self::$pluginInfo = array();
        foreach ($t as $item)
            self::$pluginInfo[$item["pName"]] = $item;
        if (($t = $db->readTable(self::$pluginTable)) === false){
            $log->log("Plugin manager", LEL_ERROR, $emsg = __METHOD__ . "(): database error: " . $db->e());
            trigger_error($emsg, E_USER_ERROR);
            return false;
        }
        self::$pluginInstanceInfo = array();
        foreach ($t as $item)
            self::$pluginInstanceInfo[(int)$item["id"]] = $item;
        if (empty(self::$pluginInfo) || empty(self::$pluginInstanceInfo)) {
            $log->log("Plugin manager", LEL_WARNING, $emsg = __METHOD__ . "(): The database contained no entries for plugins");
            trigger_error($emsg, E_USER_WARNING);
            return false;
        }
        $log->dlog("Plugin manager", LEL_NOTICE, sprintf("Successfully read information about %d plugins and %d plugin instances from the database", count(self::$pluginInfo), count(self::$pluginInstanceInfo)));
        //echo "<!--"; print_r(self::$pluginInstanceInfo); echo "-->";
        return true;
    }
    
    /**
     * Generate a new iname from a plugin's pName.
     * @param string $pName
     */
    private static function generateIname($pName) {
        global $log;
        $i = 1;
        while (self::getPluginByIname($iname = sprintf("%s_%03d", $pName, $i), true))
            $i++;
        $iname;
        $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Generated new iname '$iname' from pName '$pName'");
        return $iname;
    }
    
    /**
     * Add a new plugin to the internal array and to the database.
     * @param string $source
     * @param string[optional] $dname
     * @param string[optional] $iname
     * @return mixed Returns the new Plugin object on success or boolean false on failure. 
     */
    public static function installKind($sourceDir) {
        global $log, $db;
        
        $sourceDir = rtrim($sourceDir, "/\\");  //remove trailing (back)slashes
        $xmlFilename = "$sourceDir/pluginInfo.xml";
        
        //check if file exists and is not a directory
        if (!file_exists($xmlFilename) || is_dir($xmlFilename)) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): XML file not found ($xmlFilename)");
            return false;
        }
        
        $xml = simplexml_load_file($xmlFilename);
        if ( !$xml ) {  //if parsing of the XML file failed
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): XML file is not valid ($xmlFilename)");
            return false;
        }
        if ((string)$xml["type"] != "plugin") {  //if the XML file is not made for a plugin, even though it is named as such
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): XML file does not describe a plugin ($xmlFilename)");
            return false;
        }
        //make sure all mandatory data is included in the XML file
        if ( !(bool)$xml->pName || !(bool)$xml->minVersion || !(bool)$xml->maxVersion || !(bool)$xml->description 
            || !(bool)$xml->version || !(bool)$xml->type || !(bool)$xml->files || count($xml->files->filename) < 1 ) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): The plugin info contained in the XML file is not valid ($xmlFilename)");
            return false;
        }
        //load plugin info into variables
        $pName = (string)$xml->pName;
        $pluginVersion = (string)$xml->version;
        $minVersion = (string)$xml->minVersion;
        $maxVersion = (string)$xml->maxVersion;
        //TODO: Check for version correctness
        //$desc = (string)$xml->description; //description is currently not used
        $type = (string)$xml->type;
        $oneOfAKind = (bool)$xml->oneOfAKind;
        $files = $xml->files->filename;
            
        $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Attempting to install a plugin kind with these parameters: sourceDir=$sourceDir; pName=$pName; version=$pluginVersion");
        
        if (array_key_exists($pName, self::$pluginInfo)) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): The plugin kind $pName is already installed!");
            return false;
        }
        
        global $FBP;
        $p = $FBP . self::$commonPath . $pName;
        if (file_exists($p)) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): The destination folder for this plugin already exists (folder: $p)");
            return false;
        }
        if ($ret = mkdir($p)) {
            foreach ($files as $filename) {
                if ( strpos($filename, "/") !== false || strpos($filename, "\\") !== false) {  //if the current file goes into a sub-directory (sub-dirs do not have their own entries)
                    if (!file_exists(dirname("$p/$filename"))) {                //check if this sub-directory exists
                        $r = mkdir(dirname("$p/$filename"), 0777, true);        //and create it if necessary
                        $ret = $ret && $r;
                        $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Recursively creating directory '" . dirname("$p/$filename") . "'... " . (($r) ? "Success" : "Fail"));
                    }
                }
                $r = copy("$sourceDir/$filename", "$p/$filename");
                $ret = $ret && $r;
                $log->dlog("Component manager", LEL_NOTICE, __METHOD__ . "(): Copying file '$sourceDir/$filename' to '$p/$filename'... " . (($r) ? "Success" : "Fail"));
            }
        } else {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): Creating the destination folder for this plugin failed (folder: $p)");
            return false;
        }
        
        if (!$ret) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): Copying file to destination folder failed (folder: $p)");
            return false;
        }
        
        $query = "INSERT INTO `" . self::$pluginInfoTable . "` (`pName`, `pluginVersion`, `minVersion`, `maxVersion`, `type`, `oneOfAKind`, `alwaysOn`, `core`) 
            VALUES ('$pName', '$pluginVersion', '$minVersion', '$maxVersion', $type, '$oneOfAKind', FALSE, FALSE)";
        if (!$db->q($query)) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): Database error while installing plugin kind '$pName'; database error: " . $db->e() . "; query: " . $query);
            return false;
        }
        if (!($id = $db->getInsertId())) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): Error while retrieving database ID for plugin '$dname'");
            return false;
        }
        
        $query = "SELECT * FROM `" . self::$pluginInfoTable . "` WHERE `id`=$id";
        if (!$t = $db->getArrayA($db->q($query)) || count($t) < 1) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): Database error while installing plugin kind '$pName'; database error: " . $db->e() . "; query: " . $query);
            return false;
        }
        
        self::$pluginInfo[$pName] = $t[0];
        
        $log->dlog("Plugin manager", LEL_NOTICE, __METHOD__ . "(): Successfully installed plugin kind '$pName'; id=$id");
        return true;
    }
    
    /**
     * Remove a plugin by reference.
     * @param int $id The ID of the Plugin to be removed.
     * @return bool Returns true on success or false on failure.
     */
    public static function uninstallKind($id) {
        global $log, $db;
        $plugin = self::$pluginObjects[$id];
        if ($plugin->isCore()) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): Cannot remove plugin '" . $plugin->getDname() . "' because it is a core plugin");
            return false;
        }
        $plugin->uninstall();
        $query = sprintf("DELETE FROM `%s` WHERE `id`=%d", self::$pluginTable, $plugin->getId());
        if (!$db->q($query)) {
            $me = $db->e();
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): Error while removing component '" . $plugin->getDname() . "'; Database said: $me; Query: $query");
            return false;
        }
        global $FBP2;
        include_once ("$FBP2/includes/filesystem/recursiveDelete.php");
        if (recursiveDelete( $plugin->getFullPath() ) === false) {
            $log->log("Plugin manager", "Error", __METHOD__ . "(): Error while deleting the files of component '" . $plugin->getDname() . "'");
        }
        unset (self::$plugins[$plugin->id]);        //remove the component from the internal array
        return true;
    }
    
    public static function installInstance($pname, $dname = "", $iname = "") {
        if (empty($iname))
            $iname = self::generateIname($pName, empty($dest));
        $dest = rtrim($dest, "/\\");
        if (empty($dest))
            $dest = "$iname";
        $dest .= "/";
        if (empty($dname))
            $dname = ucfirst($iname);
            
    
        
        if (self::getPluginByIname($iname, true) !== false) {
            $log->log("Plugin manager", LEL_ERROR, __METHOD__ . "(): A plugin with the internal name '$iname' already exists. Please choose a different internal name.");
            return false;
        }
        
        
        
        global $datefmt;
        if (empty($datefmt))
            $datefmt = "YmdHis";    //default this in case it's not set (e.g. when using PluginMan from the installer)
        $installedAt = date($datefmt);
        $installedBy = 0;
        global $activeUsr;
        if (defined("__USER"))
            if (isset($activeUsr))
                $installedBy = $activeUsr->getId();
    }
    
    public static function uninstallInstance($id) {
        
    }
    
    
}

?>