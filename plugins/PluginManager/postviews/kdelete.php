<?php
/**
 * @version     2010-09-16
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

if (!$error) {
    //the rest of the installation is now done by PluginMan:
    if (PluginMan::installKind($destfolder . "/" . $foldername))
        $log->log("Plugin manager", LEL_NOTICE, "Successfully installed plugin");
}

include_once($FBP . "includes/filesystem/recursiveDelete.php");
echo "recursive delete folder";
recursiveDelete($path . "/" . $foldername);

?>