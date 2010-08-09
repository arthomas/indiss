<?php
/**
 * @version     2010-08-09
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

defined("__MAIN") or die("Restricted access.");
defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");
class_exists("Plugin") or die("Class 'Plugin' is unknown [" . __FILE__ . "]");

/**
 * 
 * @author Patrick Lehner
 * 
 */
class PluginLoginLogout extends Plugin {

    //---- Static properties ------------------------------------------------------------
    
    private static $defaultTask = "login";
    
    
    //---- Object properties ------------------------------------------------------------
        
    
    //---- Static methods ---------------------------------------------------------------
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    public function __construct($pluginInstanceInfo, $pluginInfo) {
        parent::__construct($pluginInstanceInfo, $pluginInfo);
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function hasFrontend() {
        return false;
    }
    
    public function getPluginNav() {
        return null;
    }
    
    public function initialize() {
        global $log, $db;
    }
    
    // n/a because Plugin is a Core Plugin
    public function install() {}
    
    // n/a because Plugin is a Core Plugin
    public function uninstall() {}
    
    public function processInput($postview) {
        
    }
    
    // n/a because Plugin has no frontend
    public function outputFront() {}
    
    public function outputAdmin($task = null) {
        if (is_null($task))
            $task = self::$defaultTask;
        
        include($this->getFullPath() . "/tasks/$task.php");
    }
    

}

?>