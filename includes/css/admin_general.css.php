<?php
/**
 * @version     2010-08-13
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

require_once("../loaders/loader_minimal.php");

header("CONTENT-TYPE: text/css");

?>

div#output table.fwTable {
    width: 100%;
    clear: both;
}

div#output table.fwTable tr.headingRow td {
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid black;
}

div#output table.fwTable td {
    border-bottom: 1px solid gray;
    padding: 3px 5px;
    border-left: 1px solid #BBB;
}

div#output table.fwTable td:first-child {
    padding-left: 10px;
    border-left: 2px solid black;
}

div#output table.fwTable td:last-child {
    padding-right: 10px;
    border-right: 2px solid black;
}

div#output table.fwTable tr:first-child td {
    border-top: 2px solid black;
}

div#output table.fwTable tr:last-child td {
    border-bottom: 2px solid black;
}

