<?php
/**
 * @version     2010-08-08
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
class PluginContent extends Plugin {

    //---- Static properties ------------------------------------------------------------
    
    private static $defaultTask = "list";
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $usersTable = "users";
        
    
    //---- Static methods ---------------------------------------------------------------
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    public function __construct($pluginInstanceInfo, $pluginInfo) {
        parent::__contstruct($pluginInstanceInfo, $pluginInfo);
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function hasFrontend() {
        return false;
    }
    
    public function getPluginNav() {
        $r = array(
            array("task" => "list", "label" => "User list")
        );
        return $r;
    }
    
    public function initialize() {
        global $log, $db;
    }
    
    public function install() {
        global $log, $db;
        
        
    }
    
    public function uninstall() {
        global $log, $db;
        
        /*if (!$db->dropTable($this->itemTable)) {
            
        } else {
            $log->dlog("Plugin: $this->pName", LEL_NOTICE, __CLASS__ . "::" . __METHOD__ . "(): Successfully dropped item table '$this->itemTable'");
        }*/
    }
    
    public function processInput($postview) {
        
    }
    
    public function outputFront() {}
    
    public function outputAdmin($task = null) {
        if (is_null($task))
            $task = self::$defaultTask;
        
        include($this->getFullPath() . "/tasks/$task.php");
    }
    

}

?>