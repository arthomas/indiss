<?php
/**
 * @version     2010-04-05
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

defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");
defined("__DIRAWARE") or die("Directory awareness not included [" . __FILE__ . "]");
defined("__DATABASE") or die("Database connection not included [" . __FILE__ . "]");
defined("__LANG") or die("Language file not included [" . __FILE__ . "]");
defined("__COMMAN") or die("ComMan class not included [" . __FILE__ . "]");
defined("__USRMAN") or die("UsrMan class not included [" . __FILE__ . "]");

include_once($FULL_BASEPATH . "/includes/error_handling/LiveErrorHandler.php");
include_once($FULL_BASEPATH . "/includes/logging/Logger.php");

$handler = LiveErrorHandler::getLastHandler();
if (!$handler)
    $handler = LiveErrorHandler::add("ComMan");
    
if (!$logError) {
    $logError = new Logger("error");
}
if (!$logDebug) {
    $logDebug = new Logger("debug");
}

define("__USRMGR_ADMIN",1);

$task = (!empty($_GET["task"])) ? $_GET["task"] : "list";
    
if (file_exists("tasks/$task.php")) {
    include("tasks/$task.php");
} else {
    $logError->log("User manager", "Error", "Requested task '$task' was not found", $activeUsr->getId());
}

?>