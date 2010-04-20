<?php
/**
 * @version     2010-04-19
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
defined("__COMMGR_ADMIN") or die("Include the UsrMgr backend first. [" . __FILE__ . "]");

if (($com = ComMan::getCom((int)$_POST["affectedIDs"])) !== false) {

    echo "Attempting to clone component '". $com->getDname() . "'<br />";
    
    if (($newcom = $com->duplicate("Test", "/testcom")) !== false) {
        echo "Cloning successful.<br />New component info:<br /><pre>";
        var_dump($newcom);
        echo "</pre>";
    } else {
        echo "Error while cloning component '". $com->getDname() . "'";
    }

} else {
    echo "Error while retrieving component by id '" . $_POST["affectedIDs"] . "'";
}


?>