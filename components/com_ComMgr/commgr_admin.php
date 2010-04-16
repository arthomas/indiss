<?php
/**
 * @version     2010-04-12
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

define("__COMMGR_ADMIN",1);

$task = (!empty($_GET["task"])) ? $_GET["task"] : "list";

$taskfile = dirname(__FILE__) . "/tasks/$task.php";

?>
<div id="ComMgr">
    <div id="subnav">
        <a href="?comID=<?php echo $activeCom->getId();?>&task=list">Component list</a>
        <a href="?comID=<?php echo $activeCom->getId();?>&task=install">Install new component</a>
        <a href="?comID=<?php echo $activeCom->getId();?>&task=options">Options</a>
    </div>
    <div style="clear: both; font-size: 0; max-height: 1px;">$nbsp;</div>
<?php

if (file_exists("$taskfile")) {
    include("$taskfile");
} else {
    echo '<div style="text-align: center;">Requested task \'' . $task . '\' was not found</div>';
    $logError->log("Component manager", "Error", "Requested task '$task' was not found", $activeUsr->getId());
}

?>
</div>
