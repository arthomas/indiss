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

//note that this file's scope is within a function! it is being included from within PluginDatabaseManager::processInput()
 
//This type hints enable context-completion in most IDEs:
/* @var $db MySQLConnection */
/* @var $log Logger */

defined("__MAIN") or die("Restricted access.");
class_exists("PluginDatabaseManager") or die("Class 'PluginDatabaseManager' is unknown [" . __FILE__ . "]");

if (empty($_POST["table"])) {
    $log->log("Plugin: DatabaseManager", LEL_ERROR, "No table name was passed whose entries to edit.");
    return;
}

$table = $_POST["table"];
$l = explode(",", $_POST["affectedIDs"]);

if (empty($l) || count($l) < 1) {
    $log->log("Plugin: DatabaseManager", LEL_ERROR, "No entries were passed on to edit.");
    return;
}

$cols = $db->getArrayA($db->q("SHOW COLUMNS FROM `$table`"));
if ($cols === false || count($cols) == 0) {
    $log->log("Plugin: DatabaseManager", LEL_ERROR, "Error: Could not retrieve column info.");
    return;
}

$qt = "UPDATE `$table` SET %s WHERE `id`=%s";

foreach ($l as $id) {
    $c = array();
    foreach ($cols as $col) {
        $c[] = "`{$col["Field"]}`='{$_POST[$id . "_" . $col["Field"]]}'";
    }
    $c = implode(", ", $c);
    if ($db->q($q = sprintf($qt, $c, $id)) === false) {
        $log->log("Plugin: DatabaseManager", LEL_ERROR, "Database error while updating entry '$id' in table '$table'. Database error: {$db->e()}; query: $q");
    }
}

?>