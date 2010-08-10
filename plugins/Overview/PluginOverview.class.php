<?php
/**
 * @version     2010-08-10
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
 
class PluginOverview extends Plugin {

    //---- Static properties ------------------------------------------------------------
    
    
    //---- Object properties ------------------------------------------------------------
    
    
    //---- Static methods ---------------------------------------------------------------
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function hasFrontend() {
        return false;
    }
    
    public function getPluginNav() {
        return null;
    }
    
    public function initialize() {
        ;
    }
    
    // n/a because Plugin is a Core Plugin
    public function install() {}
    
    // n/a because Plugin is a Core Plugin
    public function uninstall() {}
    
    public function processInput($postview = null) {
        ;
    }
    
    // n/a because Plugin has no frontend
    public function outputFront() {}
    
    public function outputAdmin($task) {
        PluginMan::getInfoArrays($pluginInfo, $pluginInstanceInfo);
        
        echo "<ul>\n";
        
        foreach ($pluginInfo as $plugin) {
            echo "<li><a href=\"?plugin=" . $plugin->getIname() . "\">" . $plugin->getDname() . "</a></li>\n";
        }
        
        echo "</ul>\n";
    }
    

}

?>