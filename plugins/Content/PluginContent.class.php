<?php
/**
 * @version     2010-09-29
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
    
    private static $itemTableQuery =
        "CREATE TABLE `%s` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
            `url` varchar(1023) COLLATE utf8_unicode_ci NOT NULL,
            `displaytime` int(11) NOT NULL,
            `start` datetime NOT NULL,
            `end` datetime NOT NULL,
            `type` enum('LocalPage','ExternalPage','LocalImage','ExternalImage','LocalPDF','ExternalPDF','LocalFlash','ExternalFlash','LocalOther','ExternalOther','Plugin','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown',
            `enabled` tinyint(1) NOT NULL,
            `deleted` tinyint(1) NOT NULL,
            `tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `createdBy` int(11) NOT NULL DEFAULT '0',
            `createdAt` datetime NOT NULL,
            `modifiedBy` int(11) NOT NULL DEFAULT '0',
            `modifiedAt` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
    private static $optionValues = array (
        "INSERT INTO `%s` (`name`,`value`,`comment`) VALUES ('default_display_time', '120', 'The default display time for a content item, in seconds');",
        "INSERT INTO `%s` (`name`,`value`,`comment`) VALUES ('error_display_time', '30', 'The display time for errors, e.g. if an item cannot be found');",
        "INSERT INTO `%s` (`name`,`value`,`comment`) VALUES ('max_width', 'auto', 'The maximum width of displayed images, in pixels. \"auto\" for using the frame''s width');",
        "INSERT INTO `%s` (`name`,`value`,`comment`) VALUES ('max_height', '30', 'The maximum height of displayed images, in pixels. \"auto\" for using the frame''s height');"
    );
    private static $defaultTask = "list";
    private static $tasksNeedingItemList = array("list");
    private static $tasksNeedingDeletedItemList = array("trash");
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $itemTable = "";
    private $optionTable = "";
    
    
    //---- Static methods ---------------------------------------------------------------
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    public function __construct($pluginInstanceInfo, $pluginInfo) {
        parent::__construct($pluginInstanceInfo, $pluginInfo);
        $this->itemTable = $this->iname . "_items";
        $this->optionTable = $this->iname . "_options";
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function hasFrontend() {
        return true;
    }
    
    public function getPluginNav() {
        $r = array(
            array("task" => "list", "label" => "Item list")
        );
        return $r;
    }
    
    private function getItems($deleted = false) {
        global $db;
        $query = "SELECT * FROM `$this->itemTable` WHERE `deleted`=" . (($deleted) ? "1" : "0");
        return $db->getArrayA($db->q($query));
    }
    
    public function initialize() {
        global $log, $db;
    }
    
    public function install() {
        global $log, $db;
        
        //create database tables
        $query = sprintf(self::$itemTableQuery, $this->itemTable);
        if (!$db->q($query)) {
            
            return false;
        }
        $log->dlog("Plugin: $this->pName", LEL_NOTICE, __METHOD__ . "(): Successfully created item table '$this->itemTable'");
        
        if (!$db->createNVTable($this->optionTable)) {
            
            return false;
        }
        $log->dlog("Plugin: $this->pName", LEL_NOTICE, __METHOD__ . "(): Successfully created option table '$this->optionTable'");
        
        //insert necessary option values
        foreach (self::$optionValues as $qry) {
            $query = sprintf($qry, $this->optionTable);
            if (!$db->q($query)) {
                
                return false;
            }
        }
        $log->dlog("Plugin: $this->pName", LEL_NOTICE, __METHOD__ . "(): Successfully saved all values to table '$this->optionTable'");
    }
    
    public function uninstall() {
        global $log, $db;
        
        if (!$db->dropTable($this->itemTable)) {
            
        } else {
            $log->dlog("Plugin: $this->pName", LEL_NOTICE, __METHOD__ . "(): Successfully dropped item table '$this->itemTable'");
        }
        if (!$db->dropTable($this->optionTable)) {
            
        } else {
            $log->dlog("Plugin: $this->pName", LEL_NOTICE, __METHOD__ . "(): Successfully dropped option table '$this->optionTable'");
        }
    }
    
    public function processInput($postview = null) {
        
    }
    
    public function outputFront() {
        
    }
    
    public function outputAdmin($task = null) {
        if (is_null($task))
            $task = self::$defaultTask;
        
        if (in_array($task, self::$tasksNeedingItemList))
            $items = $this->getItems();
        if (in_array($task, self::$tasksNeedingDeletedItemList))
            $deletedItems = $this->getItems(true);
        
        include($this->getFullPath() . "/tasks/$task.php");
    }
    

}

?>