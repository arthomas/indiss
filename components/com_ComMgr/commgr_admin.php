<?php
/**
 * @version     2010-04-27
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

if (!empty($_POST["postview"]) && !empty($_POST["affectedIDs"])) {
    $IDs = explode(",", $_POST["affectedIDs"]);
    
    if (!empty($IDs)) {
        switch ($_POST["postview"]) {
            case "duplicate":
                foreach ($IDs as $ID) {
                    if (($com = ComMan::getCom((int)$ID, true)) !== false) {    //if the component is there
                        if ($com->isOneOfAKind()) {                             //if the component mustn't be duplicated
                            $handler->addMsg("Component manager", "Component '" . $com->getDname() . "' cannot be duplicated", LiveErrorHandler::EK_ERROR);
                        } else {
                            $newdname = $_POST["newdname_$ID"];
                            if (empty($newdname)) {
                                $newdname = $com->getDname();
                            }
                            $newiname = $_POST["newiname_$ID"];
                            if (empty($newiname)) {
                                $newiname = $com->getComName();
                            }
                            if (($newcom = $com->duplicate($newdname, $newiname)) !== false) {
                                $newcom->enable($_POST["enable_$ID"] == "Yes");
                                $handler->addMsg("Component manager", "Component '" . $com->getDname() . " was successfully duplicated to '" . $newcom->getDname() . "'", LiveErrorHandler::EK_SUCCESS);
                            } else {
                                $handler->addMsg("Component manager", "Error while duplicating component '" . $com->getDname() . "'", LiveErrorHandler::EK_ERROR);
                            }
                        }
                    } else {    //if the component can't be found
                        $handler->addMsg("Component manager", "Could not retrieve component with id '$ID'", LiveErrorHandler::EK_ERROR);
                    }
                }   
                break;
            default:
                break;
        }
    }
    
    unset($IDs, $ID, $com, $newcom, $newdname, $newiname);
}

$task = "list";
if (!empty($_GET["task"])) {
    if ($_GET["task"][0] != '_') {      //this prevents tasks starting with an underscore from loading and thus makes such filenames available for internal helper files
        $task = $_GET["task"];
    }
}

$taskfile = dirname(__FILE__) . "/tasks/$task.php";

?>
<div id="ComMgr">
    <div id="subnav">
        <a href="?comID=<?php echo $activeCom->getId();?>&task=list">Component list</a>
        <a href="?comID=<?php echo $activeCom->getId();?>&task=install">Install new component</a>
        <a href="?comID=<?php echo $activeCom->getId();?>&task=options">Options</a>
    </div>
    <div style="clear: both; font-size: 0; max-height: 1px;">$nbsp;</div>
<!--%HANDLEROUTPUT%-->
<?php

if (file_exists("$taskfile")) {
    include("$taskfile");
} else {
    echo '<div style="text-align: center;">Requested task \'' . $task . '\' was not found</div>';
    $logError->log("Component manager", "Error", "Requested task '$task' was not found", $activeUsr->getId());
}

?>
</div>
