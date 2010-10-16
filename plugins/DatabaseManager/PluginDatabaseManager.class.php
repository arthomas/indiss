<?php
/**
 * @version     2010-10-16
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
class PluginDatabaseManager extends Plugin {

    //---- Static properties ------------------------------------------------------------
    
    private static $defaultTask = "tlist";
    
    
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
        $r = array(
            array("task" => "tlist",   	"label" => "Table list"),
            array("task" => "tadd",     "label" => "Add a table"),
        );
        return $r;
    }
    
    public function initialize() {
        //global $log, $db;
    }
    
    public function install() {
        
    }
    
    public function uninstall() {
        
    }
    
    public function processInput($postview = null) {
        global $log, $db;
        if (!is_null($postview) && !empty($postview)) {
            if (file_exists($this->getFullPath() . "/postviews/$postview.php")) {
                PluginMan::getInfoArrays($pluginInfo, $pluginInstanceInfo);
                include($this->getFullPath() . "/postviews/$postview.php");
            } else {
                $log->log("Plugin: DatabaseManager", LEL_ERROR, "The requested postview '$postview' was not found.");
            }
        }
    }
    
    // n/a because Plugin has no frontend
    public function outputFront() {}
    
    public function outputAdmin($task = null) {
        global $log, $db;
        if (is_null($task))
            $task = self::$defaultTask;
            
        PluginMan::getInfoArrays($pluginInfo, $pluginInstanceInfo);
        
        CSSJSHandler::addStyleUrl($this->getWebPath() . "/css/admin.css.php");
        CSSJSHandler::addScriptUrl($this->getWebPath() . "/js/FormSubmitFuncs.js");
        
        echo "<div class=\"pluginTask\" id=\"task_$task\">\n";
        if (file_exists($this->getFullPath() . "/tasks/$task.php")) {
            include($this->getFullPath() . "/tasks/$task.php");
        } else {
            $log->log("Plugin: DatabaseManager", LEL_ERROR, "The requested task '$task' was not found.");
            echo "Error: Task not found.";
        }
        echo "</div>\n";
    }
    

}

?>