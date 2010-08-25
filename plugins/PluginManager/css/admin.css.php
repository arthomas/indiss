<?php
/**
 * @version     2010-08-25
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

div.PluginPluginManager {
    width: 80%;
    margin: 0 auto;
}


div.PluginPluginManager div#taskAdd fieldset {
    margin-bottom: 20px;
    border: 2px solid black;
}

div.PluginPluginManager div#taskAdd fieldset#installedKindBox {
    padding-left: 0;
    padding-right: 0;
    padding-bottom: 0;
}

div.PluginPluginManager div#taskAdd fieldset#installedKindBox > legend {
    margin-left: 9px;
}

div.PluginPluginManager div#taskAdd fieldset#installedKindBox table td:first-child {
    border-left: 0 none;
    -moz-border-radius-topleft: 0;
    border-top-left-radius: 0;
    -moz-border-radius-bottomleft: 0;
    border-bottom-left-radius: 0;
}

div.PluginPluginManager div#taskAdd fieldset#installedKindBox table:not(.buttonBarTable) td:last-child {
    border-right: 0 none;
}

div.PluginPluginManager div#taskAdd fieldset#installedKindBox div#buttonbarBottom td {
    border-bottom: 0 none;
    -moz-border-radius-bottomright: 0;
    border-bottom-right-radius: 0;
    -moz-border-radius-bottomleft: 8px;
    border-bottom-left-radius: 8px;
}

div.PluginPluginManager div#taskAdd table#PluginKindList td.check {
    width: 20px;
}

div.PluginPluginManager div#taskAdd table#PluginKindList td.id {
    width: 20px;
}
