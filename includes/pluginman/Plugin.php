<?php
/**
 * @version     2010-06-07
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
 * @module      
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

require_once("PluginMan.php");
 
class Plugin {
    
    //---- Object properties ------------------------------------------------------------
    
    protected $id = 0;
    protected $installed = false;
    protected $enabled = false;
    protected $oneOfAKind = false;    //means that this plugin cannot be duplicated
    protected $alwaysOn = false;      //means that this plugin cannot be disabled
    protected $core = false;          //means that this plugin cannot be removed
    protected $dname = "";            //descriptive name
    protected $iname = "";            //internal name/"alias" -- may only contain alphanumerics, underscores and dashes
    protected $pName = "";
    protected $installedAt = 0;
    protected $installedBy = 0;
    protected $path = "";
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    /**
     * Create a plugin object with its necessary properties.
     * @param string $id
     * @param string $dname
     * @param string $iname
     * @param string $pName
     * @param string $installedAt
     * @param string $installedBy
     * @param string $path
     * @param string $enabled
     * @param string $oneOfAKind
     * @param string $alwaysOn
     * @param string $core
     */
    public function __construct($id, $dname, $iname, $pName, $installedAt, $installedBy, $path, $enabled = true, $oneOfAKind = false, $alwaysOn = false, $core = false) {
        //would have liked to keep the constructor 'private' but since ComMan now is a different class, that's not possible :(
        //thinking about making the constructor final. will see about that.....
        $this->id = $id;
        $this->dname = $dname;
        $this->iname = $iname;
        $this->pName = $pName;
        $this->installedAt = strtotime($installedAt);
        $this->installedBy = (int)$installedBy;
        $this->path = $path;
        $this->enabled = $enabled;
        $this->oneOfAKind = $oneOfAKind;
        $this->alwaysOn = $alwaysOn;
        $this->core = $core;
        $this->installed = true;
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    /**
     * Check if the plugin is installed (or only virtually in the internal array)
     * @return bool Returns true if the plugin is installed or false otherwise.
     */
    public function isInstalled() {
        return $this->installed;
    }
    
    /**
     * Check if the plugin has a front end
     * @return bool Returns true if the plugin has a front end or false otherwise.
     */
    public function hasFrontend() {
        return file_exists($this->getFullPath() . "main.php");
    }
    
    /**
     * Check if the plugin has a back end
     * @return bool Returns true if the plugin has a back end or false otherwise.
     */
    public function hasBackend() {
        return file_exists($this->getFullPath() . "admin.php");
    }
    
    /**
     * Check if the plugin is "one of a kind" (cannot be duplicated).
     * @return bool Returns true if the plugin is "one of a kind" (and cannot be
     * duplicated) or false otherwise (it CAN be duplicated).
     */
    public function isOneOfAKind() {
        //this line means that if there is no install.xml (yet), we cant duplicate it
        if (!file_exists($this->getFullPath() . "install.xml"))
            return true;
        return $this->oneOfAKind;
    }
    
    /**
     * Check if the plugin can be duplicated (provided for a more logical approach)
     * @return bool Returns true if the plugin can be duplicated or false otherwise.
     */
    public function isDuplicable() {
        return !$this->isOneOfAKind();
    }
    
    /**
     * Check if the plugin is "always on" (cannot be disabled).
     * @return bool Returns true of the plugin is "always on" or false if it can
     * be disabled.
     */
    public function isAlwaysOn() {
        return $this->alwaysOn;
    }
    
    /**
     * Check if the plugin is a core plugin (and thus cannot be removed).
     * @return bool Returns true of the plugin is a core plugin or false if
     * it can be removed. 
     */
    public function isCore() {
        return $this->core;
    }
    
    /**
     * Get the internal ID of the plugin.
     * @return int Returns the ID of the plugin.
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get the plugin name of the plugin
     * @return string Returns the plugin name of the plugin.
     */
    public function getPname() {
        return $this->pName;
    }
    
    /**
     * Get the timestamp of when the plugin was installed.
     * @return int Returns the UNIX timestamp of this plugin's installation.
     */
    public function getInstalledAt() {
        return $this->installedAt;
    }
    
    /**
     * Check who installed the plugin.
     * @return int Returns the user ID of the user who installed this plugin,
     * or 0 for "system" (e.g. during installation script).
     */
    public function getInstalledBy() {
        return $this->installedBy;
    }
    
    /**
     * Check if the plugin is enabled.
     * @return bool Returns true of the plugin is enabled or false otherwise.
     */
    public function isEnabled() {
        return $this->enabled;
    }
    
    /**
     * Enable or disable the plugin.
     * @param bool $enabled True to enable or false to disable the plugin.
     * @return bool Returns true on success or false on failure.
     */
    public function enable($enabled) {
        global $logDebug, $logError, $handler;
        if ($this->alwaysOn) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrCannotDisableCom"), $this->dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `enabled`=" . (($enabled) ? "TRUE" : "FALSE") . " WHERE `id`=" . $this->id);
        if (!$result) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang(($enabled) ? "commgrDisableComDBError" : "commgrEnableComDBError"), $this->dname, "<i>" . mysql_error() . "</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $this->enabled = $enabled;
        return true;
    }
    
    /**
     * Get the descriptive name of the plugin.
     * @return string Returns the descriptive name of the plugin.
     */
    public function getDname() {
        return $this->dname;
    }
    
    /**
     * Set the descriptive name of the plugin.
     * @param string $dname The new descriptive name for the plugin.
     * @return bool Returns true on success or false on failuer.
     */
    public function setDname($dname) {
        global $logDebug, $logError, $handler;
        $result = mysql_query("UPDATE `" . self::$dbTable . "` SET `dname`='$dname' WHERE `id`=" . $this->id);
        if (!$result) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrRenameComDBError"), $this->dname, "<i>" . mysql_error() . "</i>", "<pre>$query</pre>"), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $this->dname = $dname;
        return true;
    }
    
    /**
     * Get the internal (unique) name of the plugin.
     * @return string Returns the internal name of the plugin.
     */
    public function getIname() {
        return $this->iname;
    }
    
    /**
     * Get the relative path of the plugin's folder.
     * @return string Returns the relative path of the plugin.
     */
    public function getPath() {
        return $this->path;
    }
    
    /**
     * Get the complete web path of the plugin's folder.
     * @return string Returns the complete web path of the plugin.
     */
    public function getWebPath() {
        global $basepath;
        return $basepath . "/" . PluginMan::getCommonPath() . $this->path;
    }
    
    /**
     * Get the absolute file system path of the plugin's folder.
     * @return string Returns the absolute file system path of the plugin.
     */
    public function getFullPath() {
        global $FBP;
        return $FBP . PluginMan::getCommonPath() . $this->path;
    }
    
    /**
     * Duplicate the plugin.
     * @param string $dname The descriptive name of the duplicate.
     * @param string $iname The internal name of the duplicate.
     * @param string $dest The destination folder of the duplicate.
     */
    public function duplicate($dname = "", $iname = "", $dest = "") {
        global $logDebug, $logError, $handler;
        if ($this->oneOfAKind) {
            $handler->addMsg(lang("commgrComponentManager"), sprintf(lang("commgrCannotDuplicateCom"), $this->dname), LiveErrorHandler::EK_ERROR);
            return false;
        }
        $plugin = PluginMan::add($this->getFullPath(), $dname, $iname, $dest);
        /*if (!$com)
            $handler->addMsg("Component manager", "Duplicating component $this->dname failed", LiveErrorHandler::EK_ERROR);
        else
            $handler->addMsg("Component manager", "Component $this->dname was successfully duplicated to $com->dname", LiveErrorHandler::EK_SUCCESS);*/
        return $plugin; 
    }
}

?>