<?php
/**
 * @version     2010-07-07
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
    private static $dbTable = "plugins";
    private static $pluginInfo = array();
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
        $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Changed common path to $path");
        return true;
    }
    
    private static function loadPlugin($id) {
        if (!isset(self::$pluginObjects[$id])) {
            global $FBP;
            $pluginClass = "Plugin" . self::$pluginInfo["id"]["pName"];
            include($s = $FBP . self::$commonPath . self::$pluginInfo[$id]["pName"] . "/$pluginClass.class.php");
            self::$pluginObjects[$id] = new $pluginClass(self::$pluginInfo[$id]);
            $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Loaded Plugin class file $s and created new object");
        }
        if (!self::$pluginObjects[$id]->isInitialized()) {
            self::$pluginObjects[$id]->initialize();
            $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Initialized Plugin '" . self::$pluginInfo[$id]["iname"] . "'");
        }
        $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Successfully loaded Plugin '" . self::$pluginInfo[$id]["iname"] . "'");
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
        if (isset(self::$pluginInfo[$id])) {
            //debug-log events are located in loadPlugin()
            return self::loadPlugin($id);
        } else {
            $emsg = __CLASS__ . "::" . __METHOD__ . "(): Plugin with id '$id' was not found";
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
        foreach (self::$pluginInfo as $pI)
            if ($pI["iname"] == $iname) {
                //debug-log events are located in loadPlugin()
                return self::loadPlugin($pI["id"]);
            }
        $emsg = __CLASS__ . "::" . __METHOD__ . "(): no plugin named '$iname' was found";
        if (!$silent) {
            trigger_error($emsg, E_USER_WARNING);
            $log->log("Plugin manager", LEL_WARNING, $emsg);
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
    public static function readDB($table = null) {
        global $log, $db;
        if (!is_null($table))
            self::$dbTable = $table;
        else
            $table = self::$dbTable;
        self::$pluginInfo = array(); //reset the plugin info array -- that way this function can also refresh the DB array
        $query = "SELECT * FROM `$table`";
        $result = $db->q($query);
        if (!$result) {
            trigger_error($emsg = __CLASS__ . "::" . __METHOD__ . "(): database error: " . $db->e() . "; query: " . $query, E_USER_WARNING);
            $log->log("Plugin manager", LEL_ERROR, $emsg);
            return false;
        }
        while ($row = mysql_fetch_assoc($result)) { //fetch all resulting rows and save them into our array
            $pI = array_intersect_key($row, array("id", "dname", "iname", "pName", "installedAt", "installedBy", "enabled"));
            self::$pluginInfo[(int)$row["id"]] = $pI;
        }
        if (empty(self::$pluginInfo)) {
            trigger_error($emsg = __CLASS__ . "::" . __METHOD__ . "(): The database contained no entries for plugins", E_USER_WARNING);
            $log->log("Plugin manager", LEL_ERROR, $emsg);
            return false;
        }
        $log->dlog("Plugin manager", LEL_NOTICE, "Successfully read " . count(self::$pluginInfo) . " plugins from database table $table");
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
        $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Generated new iname '$iname' from pName '$pName'");
        return $iname;
    }
    
    /**
     * Add a new plugin to the internal array and to the database.
     * @param string $source
     * @param string[optional] $dname
     * @param string[optional] $iname
     * @param string[optional] $dest
     * @return mixed Returns the new Plugin object on success or boolean false on failure. 
     */
    public static function install($sourceDir, $dname = "", $iname = "") {
        global $log, $db;
        
        $sourceDir = rtrim($sourceDir, "/\\");
        $xmlFilename = "$sourceDir/pluginInfo.xml";
        
        if (!file_exists($xmlFilename)) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): XML file not found ($xmlFilename)");
            return false;
        }
        
        $xml = simplexml_load_file($xmlFilename);
        if ( !$xml ) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): XML file is not valid ($xmlFilename)");
            return false;
        }
        if ((string)$xml["type"] != "plugin") {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): XML file does not describe a plugin ($xmlFilename)");
            return false;
        }
        if ( !(bool)$xml->pName || !(bool)$xml->minVersion || !(bool)$xml->maxVersion || !(bool)$xml->description || !(bool)$xml->files || count($xml->files->filename) < 1 ) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): The plugin info contained in the XML file is not valid ($xmlFilename)");
            return false;
        }
        $pName = (string)$xml->pName;
        $minVersion = (string)$xml->minVersion;
        $maxVersion = (string)$xml->maxVersion;
        $desc = (string)$xml->description;
        $files = $xml->files->filename;
        if (empty($iname))
            $iname = self::generateIname($pName, empty($dest));
        $dest = rtrim($dest, "/\\");
        if (empty($dest))
            $dest = "$iname";
        $dest .= "/";
        if (empty($dname))
            $dname = ucfirst($iname);
            
        $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Attempting to install a plugin with these parameters: sourceDir=$sourceDir; dname=$dname; iname=$iname; pName=$pName; version=$version");
        
        if (self::getPluginByIname($iname, true) !== false) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): A plugin with the internal name '$iname' already exists. Please choose a different internal name.");
            return false;
        }
        
        global $FBP;
        $p = $FBP . self::$commonPath . $dest;
        if (file_exists($p)) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): The destination folder for this plugin already exists (folder: $p)");
            return false;
        }
        if ($ret = mkdir($p)) {
            foreach ($files as $filename) {
                if ( strpos($filename, "/") !== false || strpos($filename, "\\") !== false) {  //if the current file goes into a sub-directory (sub-dirs do not have their own entries)
                    if (!file_exists(dirname("$p/$filename"))) {                //check if this sub-directory exists
                        $r = mkdir(dirname("$p/$filename"), 0777, true);        //and create it if necessary
                        $ret = $ret && $r;
                        $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Recursively creating directory '" . dirname("$p/$filename") . "'... " . (($r) ? "Success" : "Fail"));
                    }
                }
                $r = copy("$sourceDir/$filename", "$p/$filename");
                $ret = $ret && $r;
                $log->dlog("Component manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Copying file '$sourceDir/$filename' to '$p/$filename'... " . (($r) ? "Success" : "Fail"));
            }
        } else {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): Creating the destination folder for this plugin failed (folder: $p)");
            return false;
        }
        
        if (!$ret) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): Copying file to destination folder failed (folder: $p)");
            return false;
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
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): Database error while installing plugin '$dname'; database error: " . $db->e() . "; query: " . $query);
            return false;
        }
        if (!($id = mysql_insert_id())) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): Error while retrieving database ID for plugin '$dname'");
            return false;
        }
        
        $plugin = new Plugin($id, $dname, $iname, $pName, $installedAt, $installedBy, $dest, true);
        self::$plugins[(int)$id] = $plugin;
        
        $log->dlog("Plugin manager", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Successfully installed plugin '$dname'; id=$id");
        return $plugin;
    }
    
    /**
     * Remove a plugin by reference.
     * @param Plugin $plugin A reference to the Plugin to be removed.
     * @return bool Returns true on success or false on failure.
     */
    public static function remove(Plugin $plugin) {
        global $log, $db;
        if ($plugin->core) {
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): Cannot remove plugin '" . $plugin->getDname() . "'");
            return false;
        }
        $query = sprintf("DELETE FROM `%s` WHERE `id`=%d", self::$dbTable, $plugin->getId());
        if (!$db->q($query)) {
            $me = $db->e();
            $log->log("Plugin manager", LEL_ERROR, __CLASS__ . "::" . __METHOD__ . "(): Error while removing component '" . $plugin->getDname() . "'; Database said: $me; Query: $query");
            return false;
        }
        global $FBP2;
        include_once ("$FBP2/includes/filesystem/recursiveDelete.php");
        if (recursiveDelete( $plugin->getFullPath() ) === false) {
            $log->log("Plugin manager", "Error", __CLASS__ . "::" . __METHOD__ . "(): Error while deleting the files of component '" . $plugin->getDname() . "'");
        }
        unset (self::$plugins[$plugin->id]);        //remove the component from the internal array
        return true;
    }
    
    /**
     * Remove a plugin by ID.
     * @param int $id The ID of the Plugin to be removed.
     * @return bool Returns true on success or false on failure.
     */
    public static function removeById($id) {
        global $log;
        if (isset(self::$plugins[$id])) {
            $log->log("Plugin manager", "Error", $emsg = __CLASS__ . "::" . __METHOD__ . "(): component ID '$id' was not found");
            trigger_error($emsg, E_USER_ERROR);
            return false;
        }
        return remove(self::$plugins[$id]);
    }
    
    /**
     * Remove a plugin by iname.
     * @param string $iname The internal name of the Plugin to be removed
     * @return bool Returns true on success or false on failure.
     */
    public static function removeByIname($iname) {
        global $log;
        $found = false;
        foreach (self::$plugins as $plugins)
            if ($plugin->iname == $iname) {
                $found = true;
                break;
            }
        if (!$found) {
            $log->log("Plugin manager", "Error", $emsg = __CLASS__ . "::" . __METHOD__ . "(): component named '$iname' was not found");
            trigger_error($emsg, E_USER_ERROR);
            return false;
        }
        return remove($plugin);
    }
    
    
}

?>