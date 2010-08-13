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

require_once("../../../includes/loaders/loader_minimal.php");

header("CONTENT-TYPE: text/css");

?>

form#loginForm {
    margin: 0 auto;
    width: 300px;
}
form#loginForm > fieldset {
    border: 1px solid black;
    padding: 10px 20px;
}
form#loginForm table {
    width: 100%;
}
form#loginForm table td {
    padding: 5px 0;
    text-align: right;
}
form#loginForm table td:first-child {
    padding-right: 10px;
    text-align: left;
}
form#loginForm input.text {
    width: 150px;
    margin: 0;
    border: 1px solid gray;
    padding: 2px;
}
form#loginForm input.submit {
    float: right;
}

