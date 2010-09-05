<?php
/**
 * @version     2010-09-04
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

//note that this file's scope is within a function! it is being included from within PluginPluginManager::processInput()
 
defined("__MAIN") or die("Restricted access.");
class_exists("PluginPluginManager") or die("Class 'PluginPluginManager' is unknown [" . __FILE__ . "]");

global $FBP;

if ($_POST["sourcetype"] == "upload") {
    if (is_uploaded_file($_FILES["pluginFile"]["tmp_name"])) {
        
        $z = new ZipArchive();
        $z->open($_FILES["pluginFile"]["tmp_name"]);
        
        /*print_r($z);
        var_dump($z);
        echo "numFiles: " . $z->numFiles . "\n";
        echo "status: " . $z->status  . "\n";
        echo "statusSys: " . $z->statusSys . "\n";
        echo "filename: " . $z->filename . "\n";
        echo "comment: " . $z->comment . "\n";
        
        for ($i=0; $i<$z->numFiles;$i++) {
            echo "index: $i\n";
            print_r($z->statIndex($i));
        }*/
        
        $path = str_replace("\\","/",$FBP . "temp");
        if ($z->extractTo($path))
            echo "ok";
            else echo "error";
        
        $z->close();
    }
} else if ($_POST["sourcetype"] == "download") {
    
}

?>