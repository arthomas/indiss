<?php
/**
 * @version     2010-04-13
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      content_admin -- HTML page manager (backend)
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

define("__CONTENT_ADMIN",1);
    
    $view = (!isset($_GET["view"])) ? "list" : $_GET["view"];
        
    switch ($view) {        //do some preparation work
        case "delete":
        case "delete2":
            if ($_POST["postview"] == "multiDelete") {
                $delcount = 0;
                foreach ($_POST as $key => $value)
                    if (strcasecmp($value,"on") == 0)
                        $delid[$delcount++] = $key;
            } else {
                $delcount = 1;
                $delid[0] = $_GET["id"];
            }
            if ($delcount) {
                $query ="SELECT * FROM `com_content` WHERE `id` IN(" . implode(",", $delid) . ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    unset($toDelete); //just to be sure
                    while ($row = mysql_fetch_object($result)) { //fetch all resulting rows
                        $toDelete[] = $row;  //and save them into our array
                    }
                }
            }
            break;
        case "restore":
            if ($_POST["postview"] == "multiRestore") {
                $recount = 0;
                foreach ($_POST as $key => $value)
                    if (strcasecmp($value,"on") == 0)
                        $reid[$recount++] = $key;
            } else {
                $recount = 1;
                $reid[0] = $_GET["id"];
            }
            if ($recount) {
                $query = "UPDATE `com_content` SET `deleted`=FALSE  wHERE `id` IN(" . implode(",", $reid) . ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    $message .= sprintf(lang("conRestoreFromTrashSuccess") . "<br />\n", $recount);  //<-- $_LANG
                }
            }
            $view = "list";
            break;
        case "edit":
            if ($_POST["postview"] == "multiEdit") {
                $editcount = 0;
                foreach ($_POST as $key => $value)
                    if (strcasecmp($value,"on") == 0)
                        $editid[$editcount++] = $key;
                
            } else {
                $editcount = 1;
                $editid[0] = $_GET["id"];
            }
            if ($editcount) {
                $query ="SELECT * FROM `com_content` WHERE `id` IN(" . implode(",", $editid) . ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    unset($toEdit); //just to be sure
                    while ($row = mysql_fetch_object($result)) { //fetch all resulting rows
                        $toEdit[] = $row;  //and save them into our array
                    }
                }
            }
            break;
    }
    switch ($_POST["postview"]) {
        case "create":
            $query = "INSERT INTO `com_content` (`name`, `url`, `displaytime`, `start`, `end`, `type`, `enabled`, `deleted`, `tags`)
                        VALUES ";
            $c = 0; //counter for actual number of added pages
            //var_dump($_POST);
            for ($i = 0; $i < $_POST["new_pages"]; $i++) {
                if ( !empty($_POST["URL$i"]) && !empty($_POST["tags$i"]) ) {
                    //determine the link type
                    
                    $URL = $_POST["URL$i"];
                    if ( preg_match('/^http:\/\/[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,5}/i', $URL) ) {
                        if ( preg_match('/\.(?:bmp|gif|png|jpg|jpeg|svg)$/i', $URL) ) {
                            $type = "ExternalImage";
                        } else if ( preg_match('/\.(?:html|html|php|shtml)$/i', $URL) ) {
                            $type = "ExternalPage";
                        } else if ( preg_match('/\.(?:pdf)$/i', $URL) ) {
                            $type = "ExternalPDF";
                        } else if ( preg_match('/\.(?:swf)$/i', $URL) ) {
                            $type = "ExternalFlash";
                        } else {
                            $type = "ExternalOther";
                        }
                    } else if ( preg_match('/^[a-zA-Z]+:\/\//i', $URL) ) {
                        $type = "ignore";
                    } else {
                        if ( preg_match('/\.(?:bmp|gif|png|jpg|jpeg|svg)$/i', $URL) ) {
                            $type = "LocalImage";
                        } else if ( preg_match('/\.(?:html|html|php|shtml)$/i', $URL) ) {
                            $type = "LocalPage";
                        } else if ( preg_match('/\.(?:pdf)$/i', $URL) ) {
                            $type = "LocalPDF";
                        } else if ( preg_match('/\.(?:swf)$/i', $URL) ) {
                            $type = "LocalFlash";
                        } else {
                            $type = "LocalOther";
                        }
                    }
                    
                    if ( $type != "ignore" ) {
                        if ( $c > 0 )
                            $query .= ",";
                        $query .= "('" .
                            $_POST["name$i"] . "', '" .
                            $_POST["URL$i"] . "', '" .
                            $_POST["disptime$i"] . "', '" .
                            $_POST["start".$i."result"] . "', '" .
                            $_POST["end".$i."result"] . "', '" .
                            $type . "', " .
                            ( ($_POST["enabled$i"]) ? "TRUE" : "FALSE" ) . ", FALSE, '" .
                            $_POST["tags$i"] . "')";
                        $c++;
                    }
                }
            }
            
            $result = mysql_query($query);
            if (!$result) {
                $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
            } else {
                $message .= sprintf(lang("conCreateSuccess") . "<br />\n", $c);           //  <<-----  $_LANG
            }
            unset($c, $result);
            break;
        case "recycleYes":
            if ($_POST["delcount"] > 0) {
                $query = "UPDATE `com_content` SET `deleted`=TRUE  WHERE `id` IN(";
                for ($i = 0; $i < $_POST["delcount"]; $i++)
                    $query .= $_POST["id$i"] . ",";
                $query = rtrim($query, ",");
                $query .= ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    $message .= sprintf(lang("conMoveToTrashSuccess") . "<br />\n", $_POST["delcount"]);  //  <<-----  $_LANG
                }
            }
            break;
        case "deletePermYes":
            if ($_POST["delcount"] > 0) {
                $query = "DELETE FROM `com_content` WHERE `id` IN(";
                for ($i = 0; $i < $_POST["delcount"]; $i++)
                    $query .= $_POST["id$i"] . ",";
                $query = rtrim($query, ",");
                $query .= ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    $message .= sprintf(lang("conPermDeleteSuccess") . "<br />\n", $_POST["delcount"]);   //  <<-----  $_LANG
                }
            }
            break;
        case "edit":
            if ($_POST["editcount"] > 0) {
                $c = 0;
                for ($i = 0; $i < $_POST["editcount"]; $i++) {
                    if ( !empty($_POST["URL$i"]) && !empty($_POST["tags$i"]) && !empty($_POST["disptime$i"]) && !empty($_POST["start".$i."result"]) && !empty($_POST["end".$i."result"]) ) {
                        $query = "UPDATE `com_content` SET ";
                        $query .= "`name`='" . $_POST["name$i"] . "',";
                        $query .= "`url`='" . $_POST["URL$i"] . "',";
                        $query .= "`displaytime`='" . $_POST["disptime$i"] . "',";
                        $query .= "`start`='" . $_POST["start".$i."result"] . "',";
                        $query .= "`end`='" . $_POST["end".$i."result"] . "',";
                        $query .= "`enabled`=" . ( ($_POST["enabled$i"]) ? "TRUE" : "FALSE" ) . ",";
                        $query .= "`tags`=" . $_POST["tags$i"];
                        if ( isset($_POST["wasdeleted$i"])) {
                            $query .= ",`deleted`=";
                            $query .= (isset($_POST["deleted$i"])) ? "TRUE" : "FALSE";
                        }
                        $query .= " WHERE `id`=" . $_POST["id$i"];
                        $result = mysql_query($query);
                        if (!$result) {
                            $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                            $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                        } else {
                            $c++;
                        }
                    }
                }
                $message .= sprintf(lang("conEditSaveSuccess") . "<br />\n", $c);    //  <<-----  $_LANG
                unset($c);
            }
            break;
        default:
            break;
    }
    $query = "SELECT * FROM `com_content`";

    $result = mysql_query($query);
    
    if (!$result) { 
        $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
        $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
    } else {
        unset($rows); //just to be sure
        while ($row = mysql_fetch_object($result)) { //fetch all resulting rows
            $rows[] = $row;  //and save them into our array
        }
        
        unset($past, $preset, $future); //just to be sure
        if (isset($rows))
            foreach ($rows as $value) {
                if ($value->deleted)
                   $deleted[] = $value;
                else if (strtotime($value->end) < time())
                   $past[] = $value;
                else if (strtotime($value->start) > time())
                   $future[] = $value;
                else
                   $present[] = $value;
            }
    }
    
    
    

    
    
    /*Some functions*/
    function contentmanNavLink($_view, $name) {
        global $activeCom;
        echo ($_view == $GLOBALS["view"]) ? "<span style=\"font-weight: bold;\">$name</span>" : "<a href=\"?comID=".$activeCom->getId()."&view=$_view\">$name</a>";
    }
    
    function contentmanOutputTableHead ($indent = 0) {
        for ($i = 0; $i < $indent; $i++) $space .= " ";
        $output = 
            "$space<tr>\n" .
            "$space    <th class=\"tName\">" . lang("conName") . "</th>\n" .                                                                    //  <<-----  $_LANG
            "$space    <th class=\"tURL\">" . lang("conURL") . "</th>\n" .                                                                      //  <<-----  $_LANG
            "$space    <th class=\"tTags\">" . lang("conTags") . "</th>\n" .                                                                    //  <<-----  $_LANG
            "$space    <th class=\"tType\">" . lang("conType") . "</th>\n" .                                                                    //  <<-----  $_LANG
            "$space    <th class=\"tDispTime\">" . lang("conDispTime") . "</th>\n" .                                                            //  <<-----  $_LANG
            "$space    <th class=\"tFrom\">" . lang("conDispFrom") . "</th>\n" .                                                                //  <<-----  $_LANG
            "$space    <th class=\"tUntil\">" . lang("conDispUntil") . "</th>\n" .                                                              //  <<-----  $_LANG
            "$space    <th class=\"tEdit\" title=\"" . lang("conEdit") . "\">" . lang("conEditShort") . "</th>\n" .                             //  <<-----  $_LANG
            "$space    <th class=\"tDelete\" title=\"" . lang("conDelete") . "\">" . lang("conDeleteShort") . "</th>\n" .                       //  <<-----  $_LANG
            "$space    <th class=\"tCheck\"></th>\n" .
            "$space</tr>\n";
        echo $output;
    }
    
    function contentmanOutputList($list, $indent=0, $withselect=1) {
        global $activeCom;
        $comId = $activeCom->getId();
        for ($i = 0; $i < $indent; $i++) $output .= " ";
        if  (!isset($GLOBALS[$list]))
            echo $output . "<tr class=\"none\"><td colspan=\"" . (($withselect == 2) ? 11 : 10) . "\">". lang("genNone") . "</td></tr>\n";
        else
            foreach ($GLOBALS[$list] as $value) {
                unset($output);
                $output .= "<tr class=\"$list\">" .
                    "<td class=\"tName\">$value->name</td>" .
                    "<td class=\"tURL\">$value->url</td>" .
                    "<td class=\"tTags\">$value->tags</td>" .
                    "<td class=\"tType\">" . lang("conType" . ( (empty($value->type)) ? "Unknown": $value->type ) ) . "</td>" .
                    "<td class=\"tDispTime\">$value->displaytime s</td>" .
                    "<td class=\"tFrom\">$value->start</td>" .
                    "<td class=\"tUntil\">$value->end</td>";
                switch ($withselect) {
                    case 1:
                        $output .=
                            "<td class=\"tEdit\"><a href=\"?comID=$comId&view=edit&id=$value->id\" title=\"" . lang("conEdit") . "\">" . lang("conEditShort") . "</a></td>" .              //  <<-----  $_LANG
                            "<td class=\"tDelete\"><a href=\"?comID=$comId&view=delete&id=$value->id\" title=\"" . lang("conDelete") . "\">" . lang("conDeleteShort") . "</a></td>" .      //  <<-----  $_LANG
                            "<td class=\"tCheck\"><input type=\"checkbox\" name=\"$value->id\" title=\"" . lang("conSelectMultiple") . "\"></td>";                                              //  <<-----  $_LANG
                        break;
                    case 2:
                        $output .=
                            "<td class=\"tEdit\"><a href=\"?comID=$comId&view=edit&id=$value->id\" title=\"" . lang("conEdit") . "\">" . lang("conEditShort") . "</a></td>" .              //  <<-----  $_LANG
                            "<td class=\"tRestore\"><a href=\"?comID=$comId&view=restore&id=$value->id\" title=\"" . lang("conRestore") . "\">" . lang("conRestoreShort") . "</a></td>" .  //  <<-----  $_LANG
                            "<td class=\"tDelete\"><a href=\"?comID=$comId&view=delete2&id=$value->id\" title=\"" . lang("conDelete2") . "\">" . lang("conDelete2Short") . "</a></td>" .   //  <<-----  $_LANG
                            "<td class=\"tCheck\"><input type=\"checkbox\" name=\"$value->id\" title=\"" . lang("conSelectMultiple") . "\" /></td>";                                            //  <<-----  $_LANG
                        break;
                }
                $output .= "</tr>\n";
                echo $output;
            }
    }
    
    
?>
<?php if (isset($message)) { echo "<div id=\"messageBar\">$message</div>\n            "; } ?>
<div id="content">
                <div><?php lang_echo("conPageManHeadline");?></div>
                <div id="contentNav">
                    <?php contentmanNavLink("list",      lang("conNavList")); ?> 
                    <?php contentmanNavLink("create",    lang("conNavCreate")); ?> 
                    <?php contentmanNavLink("deleted",   lang("conNavTrash")); ?> 
                    <?php contentmanNavLink("options",   lang("conNavOptions")); ?> 
                </div>
<?php 

//----LIST------------------------------------------------------------------------------------------------------------------------------------------

if ($view == "list") {
    include(dirname(__FILE__)."/tasks/list.php");
} 

//----CREATE----------------------------------------------------------------------------------------------------------------------------------------

else if ( ( $create = ($view == "create") ) || ( $edit = ($view == "edit") ) ) {
    //Note: this if-condition above will at the same time check if we want to create new items or edit existing ones and remember which of the two
    //      it is; the purpose of this is to make it simpler to react to either case in the combined code that follows.
    //      Instead of having to use 'if ($view == "create") {...}' we can now just used 'if ($create) {...}'
    include(dirname(__FILE__)."/tasks/$view.php");
}

//----DELETE/DELETE2--------------------------------------------------------------------------------------------------------------------------------

else if (($view == "delete") || ($view == "delete2")) { ?>
                <div id="contentDeleteTop">
                    <form id="contentDeleteForm" action="?component=content<?php if ($view == "delete2") echo "&view=deleted"; ?>" method="post">
                        <div id="contentDeleteButtonBar">
                            <input type="hidden" name="postview" value="unset" id="postview" />
                            <input type="submit" value="<?php lang_echo("conYesReallyDelete");?>" onclick="document.getElementById('postview').value = '<?php echo ($view == "delete") ? "recycleYes" : "deletePermYes"; ?>';" />
                            <input type="submit" value="<?php lang_echo("conNoDontDelete");?>" />
                        </div>
                        <?php lang_echo(($view == "delete") ? "conMoveToTrash?" : "conDeletePermanently?"); ?> 
                        <input type="hidden" name="delcount" value="<?php echo $delcount; ?>" />
<?php for ($i = 0; $i < $delcount; $i++) echo "                        <input type=\"hidden\" name=\"id$i\" value=\"$delid[$i]\" />";?>
                    </form>
                </div>
                <fieldset id="contentDeleteList"><legend><?php lang_echo("conPagesToDelete");?></legend>
                    <table id="contentTable">
                        <thead>
                            <tr><th class="tName"><?php lang_echo("conName");?></th><th class="tURL"><?php lang_echo("conURL");?></th><th class="tType"><?php lang_echo("conType");?></th><th class="tDispTime"><?php lang_echo("conDispTime");?></th><th class="tFrom"><?php lang_echo("conDispFrom");?></th><th class="tUntil"><?php lang_echo("conDispUntil");?></th></tr>
                        </thead>
                        <tbody>
<?php contentmanOutputList("toDelete", 32, 0);?> 
                        </tbody>
                    </table>
                </fieldset>
<?php } 

//----DELETED: TRASH BIN----------------------------------------------------------------------------------------------------------------------------

else if ($view == "deleted") {
    include(dirname(__FILE__)."/tasks/trash.php");
}
else { ?>
            Error: The view you requested is unknown.
<?php } ?>
            </div>