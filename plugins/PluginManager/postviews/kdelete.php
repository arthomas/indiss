<?php
/**
 * @version     2010-09-17
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

$l = explode(",", $_POST["affectedIDs"]);
$m = array();
foreach ($l as $id) {
    $x = null;
    foreach ($pluginInfo as $p) {
        if ($p["id"] == $id)
            $x = $p["pName"];
    }
    if (!is_null($x))
        $m[] = $x;
}

if (count($m) == 0) {
    $log->log("Plugin manager", LEL_NOTICE, "No plugin kinds were selected for deletion.");
} else {
    //the rest of the uninstallation is now done by PluginMan:
    foreach ($m as $p)
        if (PluginMan::uninstallKind($p))
            $log->log("Plugin manager", LEL_NOTICE, "Successfully uninstalled plugin '$p'");
}

?>