<?php
/**
 * @version     2010-07-13
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
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $itemTable;
    private $optionTable;
    
    
    //---- Static methods ---------------------------------------------------------------
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    public function __construct($pluginInstanceInfo, $pluginInfo) {
        parent::__contstruct($pluginInstanceInfo, $pluginInfo);
        $this->itemTable = $this->iname . "_items";
        $this->optionTable = $this->iname . "_options";
    }
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function hasFrontend() {
        return true;
    }
    
    public function initialize() {
        
    }
    
    public function install() {
        global $log, $db;
        
        //create database tables
        $query = "CREATE TABLE IF NOT EXISTS `com_content_1` (
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
        if (!$db->q()) {
            
        }
    }
    
    public function uninstall() {
        
    }
    
    public function outputFront() {
        
    }
    
    public function outputAdmin() {
        
    }
    

}

?>