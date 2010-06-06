<?php 
/**
 * @version     2010-02-25
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
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

include_once("config/config.php");
include_once("includes/dir_awareness.php");
include_once("includes/database.php");

include_once($FULL_BASEPATH . "/includes/logging/Logger.php");
$logError = new Logger("error");
$logDebug = new Logger("debug");

//i18n
$lang = $db->getOption("frontend_lang", $db->getOption("default_lang", "en"));
include($FULL_BASEPATH . "/lang/lang.php");

include_once($FULL_BASEPATH . "/components/com_ComMgr/ComMan.php");
ComMan::readDB("components");



$view = "default_view";
if ( !empty( $_GET["view"] ) ) {
    if ( file_exists( "views/".$_GET["view"]."/main.php" ) ) {
        $view = $_GET["view"];
    }
}


include("views/$view/main.php");


if (isset($dbconnected))
	mysql_close();

?>