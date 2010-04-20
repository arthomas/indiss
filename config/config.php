<?php
/**
 * @version     2010-04-20
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
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

//$configfile    = true;          //legacy
define("__CONFIGFILE", 1);

include(dirname(__FILE__) . "/version.php");


if (!file_exists(dirname(__FILE__) . "/config.xml")) {
   die("Config file not found.");
}
$xml = simplexml_load_file(dirname(__FILE__) . "/config.xml");
if (!$xml) {
    die("Error while reading config file");
}

foreach ($xml as $option) {
    switch((string)$option["type"]) {
        case "string":
            $value = (string)$option["value"];
            break;
        case "bool":
            $value = (bool)$option["value"];
            break;
        case "int":
            $value = (int)$option["value"];
        default:
            $value = $option["value"];
            break;
    }
    $GLOBALS[(string)$option["name"]] = $value;
}

unset($xml, $option, $value);

?>