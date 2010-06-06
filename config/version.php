<?php
/**
 * @version     2010-04-20
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");

$main_ver = 0;
$sub_ver  = 1;
$dev_ver  = 1;

$ver_sep  = ".";

function __version() {
    global $main_ver, $sub_ver, $dev_ver, $ver_sep;
	$ver = (string)$main_ver . $ver_sep . (string)$sub_ver . $ver_sep . (string)$dev_ver;
	return (string) $ver;
}

function __versionID() {
    $ver = $main_ver * 10000 + $sub_ver * 100 + $dev_ver;
    return (int)$ver;
}

?>