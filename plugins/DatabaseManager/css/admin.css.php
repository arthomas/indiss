<?php
/**
 * @version     2010-10-13
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
 
define("__MAIN", 1);

require_once("../../../includes/loaders/loader_minimal.php");

header("CONTENT-TYPE: text/css");

?>

div.PluginDatabaseManager {
    width: 80%;
    margin: 0 auto;
}

div.PluginDatabaseManager div#task_tlist table#TableList tr:not(.headingRow):hover {
	background-color: #FFD;
}

div.PluginDatabaseManager div#task_tlist table#TableList td.check,
div.PluginDatabaseManager div#task_tlist table#TableList td.insert,
div.PluginDatabaseManager div#task_tlist table#TableList td.structure,
div.PluginDatabaseManager div#task_tlist table#TableList td.empty,
div.PluginDatabaseManager div#task_tlist table#TableList td.drop {
	width: 20px;
	text-align: center;
}

div.PluginDatabaseManager div#task_tlist table#TableList td.numEntries {
	text-align: right;
	width: 50px;
}

div.PluginDatabaseManager div#task_tlist table#TableList td.createdAt,
div.PluginDatabaseManager div#task_tlist table#TableList td.updatedAt,
div.PluginDatabaseManager div#task_tlist table#TableList td.engine {
	text-align: center;
	width: 150px;
}
