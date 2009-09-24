<?php
/**
 * @version     2009-09-24
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      tickers_admin -- Ticker Manager (backend)
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
                $query ="SELECT * FROM `com_tickers` WHERE `id` IN(" . implode(",", $delid) . ")";
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
                $query = "UPDATE `com_tickers` SET `deleted`=FALSE  wHERE `id` IN(" . implode(",", $reid) . ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    $message .= sprintf(lang("ticRestoreFromTrashSuccess") . "<br />\n", $recount);  //<-- $_LANG
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
                $query ="SELECT * FROM `com_tickers` WHERE `id` IN(" . implode(",", $editid) . ")";
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
            $query = "INSERT INTO `com_tickers` (`caption`, `content`, `start`, `end`, `deleted`)
                        VALUES ";
            $c = 0; //counter for actual number of added tickers
            //var_dump($_POST);
            for ($i = 0; $i < $_POST["new_tickers"]; $i++) {
                if ((!empty($_POST["caption$i"])) || (!empty($_POST["content$i"]))) {
                    if ($c > 0)
                        $query .= ",";
                    $query .= "('" . $_POST["caption".$i] . "', '" . $_POST["content".$i] . "', '" . $_POST["start".$i."result"] . "', '" . $_POST["end".$i."result"] . "', FALSE)";
                    $c++;
                }
            }
            $result = mysql_query($query);
            if (!$result) {
                $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
            } else {
                $message .= sprintf(lang("ticCreateSuccess") . "<br />\n", $c);           //  <<-----  $_LANG
            }
            unset($c, $result);
            break;
        case "recycleYes":
            if ($_POST["delcount"] > 0) {
                $query = "UPDATE `com_tickers` SET `deleted`=TRUE  WHERE `id` IN(";
                for ($i = 0; $i < $_POST["delcount"]; $i++)
                    $query .= $_POST["id$i"] . ",";
                $query = rtrim($query, ",");
                $query .= ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    $message .= sprintf(lang("ticMoveToTrashSuccess") . "<br />\n", $_POST["delcount"]);  //  <<-----  $_LANG
                }
            }
            break;
        case "deletePermYes":
            if ($_POST["delcount"] > 0) {
                $query = "DELETE FROM `com_tickers` WHERE `id` IN(";
                for ($i = 0; $i < $_POST["delcount"]; $i++)
                    $query .= $_POST["id$i"] . ",";
                $query = rtrim($query, ",");
                $query .= ")";
                $result = mysql_query($query);
                if (!$result) {
                    $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                    $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                } else {
                    $message .= sprintf(lang("ticPermDeleteSuccess") . "<br />\n", $_POST["delcount"]);   //  <<-----  $_LANG
                }
            }
            break;
        case "edit":
            if ($_POST["editcount"] > 0) {
                $c = 0;
                for ($i = 0; $i < $_POST["editcount"]; $i++) {
                    $query = "UPDATE `com_tickers` SET";
                    if (isset($_POST["caption$i"])) $query .= "`caption`='" . $_POST["caption$i"] . "',";
                    if (isset($_POST["content$i"])) $query .= "`content`='" . $_POST["content$i"] . "',";
                    if (isset($_POST["start$i"])) $query .= "`start`='" . $_POST["start$i"] . "',";
                    if (isset($_POST["end$i"])) $query .= "`end`='" . $_POST["end$i"] . "',";
                    if (isset($_POST["deleted$i"])) {
                        $query .= "`deleted`=";
                        $query .= ($_POST["deleted$i"] == "on") ? "TRUE" : "FALSE";
                    }
                    $query = rtrim($query, ",");
                    $query .= "WHERE `id`=" . $_POST["id$i"];
                    $result = mysql_query($query);
                    if (!$result) {
                        $message .= sprintf(lang("errDBError") . "<br />\n", mysql_error());      //  <<-----  $_LANG
                        $message .= sprintf(lang("errDBErrorQry") . "<br />\n", $query);          //  <<-----  $_LANG
                    } else {
                        $c++;
                    }
                }
                $message .= sprintf(lang("ticEditSaveSuccess") . "<br />\n", $c);    //  <<-----  $_LANG
                unset($c);
            }
            break;
        default:
            break;
    }
    $query = "SELECT * FROM `com_tickers`";

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
        foreach ($rows as $value) {
            if ($value->deleted)
               $deleted[] = $value;
            else if ( strtotime($value->end) < time() )
               $past[] = $value;
            else if ( strtotime($value->start) > time() )
               $future[] = $value;
            else
               $present[] = $value;
        }
    }
    
    
    
    

    
    
    /*Some functions*/
    function tickermanNavLink($_view, $name) {
        echo ($_view == $GLOBALS["view"]) ? "<span style=\"font-weight: bold;\">$name</span>" : "<a href=\"?component=tickers&view=$_view\">$name</a>";
    }
    
    function tickermanOutputList($list, $indent=0, $withselect=1) {
        for ($i = 0; $i < $indent; $i++) $output .= " ";
        if  (!isset($GLOBALS[$list]))
            echo $output . "<tr class=\"none\"><td colspan=\"7\">". lang("genNone") . "</td></tr>\n";
        else
            foreach ($GLOBALS[$list] as $value) {
                unset($output);
                $output .= "<tr class=\"$list\">" .
                    "<td class=\"tName\">$value->caption</td>" .
                    "<td class=\"tContent\">$value->content</td>" .
                    "<td class=\"tFrom\">$value->start</td>" .
                    "<td class=\"tUntil\">$value->end</td>";
                switch ($withselect) {
                    case 1:
                        $output .=
                            "<td class=\"tEdit\"><a href=\"?component=tickers&view=edit&id=$value->id\" title=\"" . lang("ticEdit") . "\">" . lang("ticEditShort") . "</a></td>" .
                            "<td class=\"tDelete\"><a href=\"?component=tickers&view=delete&id=$value->id\" title=\"" . lang("ticDelete") . "\">" . lang("ticDeleteShort") . "</a></td>" .
                            "<td class=\"tCheck\"><input type=\"checkbox\" name=\"$value->id\" title=\"" . lang("ticSelectMultiple") . "\"></td>";
                        break;
                    case 2:
                        $output .=
                            "<td class=\"tEdit\"><a href=\"?component=tickers&view=edit&id=$value->id\" title=\"" . lang("ticEdit") . "\">" . lang("ticEditShort") . "</a></td>" .
                            "<td class=\"tRestore\"><a href=\"?component=tickers&view=restore&id=$value->id\" title=\"" . lang("ticRestore") . "\">" . lang("ticRestoreShort") . "</a></td>" .
                            "<td class=\"tDelete\"><a href=\"?component=tickers&view=delete2&id=$value->id\" title=\"" . lang("ticDelete2") . "\">" . lang("ticDelete2Short") . "</a></td>" .
                            "<td class=\"tCheck\"><input type=\"checkbox\" name=\"$value->id\" title=\"" . lang("ticSelectMultiple") . "\" /></td>";
                        break;
                }
                $output .= "</tr>\n";
                echo $output;
            }
    }
    
    
?>
<?php if (isset($message)) { echo "<div id=\"messageBar\">$message</div>\n            "; } ?>
<div id="tickers">
                <div><?php lang_echo("ticTickManHeadline");?></div>
                <div id="tickermanNav">
                    <?php tickermanNavLink("list",      lang("ticNavList")); ?> 
                    <?php tickermanNavLink("create",    lang("ticNavCreate")); ?> 
                    <?php tickermanNavLink("deleted",   lang("ticNavTrash")); ?> 
                    <?php tickermanNavLink("options",   lang("ticNavOptions")); ?> 
                </div>
<?php 
//----LIST------------------------------------------------------------------------------------------------------------------------------------------
if ($view == "list") { ?>
                <fieldset id="tickerList"><legend><?php lang_echo("ticExistingTickers");?></legend>
                    <table id="tickersHead" summary="" border="0" cellpadding="0" cellspacing="0">
                        <tr><th class="tName"><?php lang_echo("ticCaption");?></th><th class="tContent"><?php lang_echo("ticContent");?></th><th class="tFrom"><?php lang_echo("ticDispFrom");?></th><th class="tUntil"><?php lang_echo("ticDispUntil");?></th><th class="tEdit" title="<?php lang_echo("ticEdit");?>"><?php lang_echo("ticEditShort");?></th><th class="tDelete" title="<?php lang_echo("ticDelete");?>"><?php lang_echo("ticDeleteShort");?></th><th class="tCheck"></th></tr>
                    </table>
                    <form id="tickerListForm" action="?component=tickers" method="post">
                        <div id="tickersDiv">
                            <table id="tickers">
                                <tr class="category"><td colspan="7"><?php lang_echo("ticPastTickers");?></td></tr>
<?php tickermanOutputList("past", 32);?>
                                <tr class="category"><td colspan="7"><?php lang_echo("ticPresentTickers");?></td></tr>
<?php tickermanOutputList("present", 32);?>
                                <tr class="category"><td colspan="7"><?php lang_echo("ticFutureTickers");?></td></tr>
<?php tickermanOutputList("future", 32);?>
                            </table>
                        </div>
                        <div id="tickerListButtons">
                            <input type="hidden" name="postview" value="unset" id="postview" />
                            <input type="submit" value="<?php lang_echo("ticEditSelected");?>" onclick="this.form.action = './?component=tickers&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("ticDeleteSelected");?>" onclick="this.form.action = './?component=tickers&view=delete'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                    </form>
                </fieldset>
<?php } 
//----CREATE----------------------------------------------------------------------------------------------------------------------------------------
else if ($view == "create") {
    $new_tickers = (isset($_POST["new_tickers"])) ? $_POST["new_tickers"] : 2 ;
?>
                <div id="tickerCreateTop">
                    <form id="tickerCreateRestartForm" action="?component=tickers&view=create" method="post">
                        <?php lang_echo("ticReloadCreate1");?> 
                        <select name="new_tickers" onchange="this.form.submit();">
<?php foreach (array(1,2,3,4,5,10,15,20,30,40,50) as $value) {
    $selected = ($value == $new_tickers) ? " selected=\"selected\"" : "";
    echo "                            <option value=\"$value\"$selected>$value</option>\n";
}
?>
                        </select>
                        <?php lang_echo("ticReloadCreate2");?> 
                    </form>
                    <?php lang_echo("ticEmptyCapConNotice");?> 
                </div>
                <script type="text/javascript">
                function updateResult2 ( Index, StartOrEnd ) {
                	document.getElementById(StartOrEnd + Index + "result").value = document.getElementById(StartOrEnd + Index + "dateval").value + ' ' + document.getElementById(StartOrEnd + Index + "timeval").value;
                }
                
                function updateResult ( obj, Index, StartOrEnd, DateOrTime ) {
                    var custom = document.getElementById(StartOrEnd + Index + DateOrTime + "custom");
                    if ( obj.value == "custom" ) {
                        custom.disabled = false;
                    	document.getElementById(StartOrEnd + Index + DateOrTime + "val").value = custom.value;
                    } else {
                        custom.disabled = true;
                        document.getElementById(StartOrEnd + Index + DateOrTime + "val").value = obj.value;
                    }
                	updateResult2 ( Index, StartOrEnd );
                }
                
                function updateResultDateCustom ( obj, Index, StartOrEnd ) {
                    if ( String(obj.value).search(/^2\d{3}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1-2]\d|3[0-1])$/) != -1 ) {
                    	document.getElementById(StartOrEnd + Index + "dateval").value = obj.value;
                    	updateResult2 ( Index, StartOrEnd );
                    }
                }

                function updateResultTimeCustom ( obj, Index, StartOrEnd ) {
                    if ( String(obj.value).search(/^(?:[0-1]\d|2[0-3])(?::(?:[0-5]\d)){2}$/) != -1 ) {
                        document.getElementById(StartOrEnd + Index + "timeval").value = obj.value;
                        updateResult2 ( Index, StartOrEnd );
                    }
                }
                </script>
                <form id="tickerCreateForm" action="?component=tickers&view=list" method="post">
                    <input type="hidden" name="postview" value="create" />
                    <div id="tickerCreateButtonBar"><input type="submit" value="<?php lang_echo("genSave");?>" /></div>
                    <input type="hidden" name="new_tickers" value="<?php echo $new_tickers; ?>" />
<?php for ($i = 0; $i < $new_tickers; $i++) {?>
                    <fieldset class="tickerCreate"><legend><?php lang_echo("ticCreateTicker");?></legend>
                        <table class="tickerCreateTable" summary="" border="0" cellpadding="0" cellspacing="0">
                            <tr><td><label for="caption<?php echo $i;?>"><?php lang_echo("ticCaption");?>:</label></td><td><input type="text" name="caption<?php echo $i;?>" /></td></tr>
                            <tr><td><label for="content<?php echo $i;?>"><?php lang_echo("ticContent");?>:</label></td><td><textarea name="content<?php echo $i;?>"></textarea></td></tr>
                            <tr>
                                <td><label><?php lang_echo("ticDispFrom");?>:</label></td>
                                <td rowspan="2">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <th>Date:</th>
                                                <td><input type="radio" name="start<?php echo $i;?>date" value="<?php echo date("Y-m-d"); ?>" checked="checked" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" />Today</td>
                                                <td><input type="radio" name="start<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+1day")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" />Tomorrow</td>
                                                <td><input type="radio" name="start<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+2days")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" />In 2 days</td>
                                                <td><input type="radio" name="start<?php echo $i;?>date" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" /><label for="start<?php echo $i;?>date">Custom:</label></td>
                                                <td>
                                                    <input type="text" name="start<?php echo $i;?>datecustom" value="<?php echo date("Y-m-d"); ?>" id="start<?php echo $i;?>datecustom" disabled="disabled" onchange="updateResultDateCustom(this, '<?php echo $i;?>', 'start');" />
                                                    <input type="hidden" name="start<?php echo $i;?>dateval" value="<?php echo date("Y-m-d"); ?>" id="start<?php echo $i;?>dateval" />
                                                </td>
                                                <td rowspan="2">Resulting time stamp: <input type="text" name="start<?php echo $i;?>result" value="<?php echo date("Y-m-d H:i:s", strtotime("today 06:00")); ?>" readonly="readonly" id="start<?php echo $i;?>result" /></td>
                                            </tr>
                                            <tr>
                                                <th>Time:</th>
                                                <td><input type="radio" name="start<?php echo $i;?>time" value="06:00:00" checked="checked" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');" />Morning</td>
                                                <td><input type="radio" name="start<?php echo $i;?>time" value="09:30:00" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');" />Morning break</td>
                                                <td><input type="radio" name="start<?php echo $i;?>time" value="12:00:00" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');" />Noon</td>
                                                <td><input type="radio" name="start<?php echo $i;?>time" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');" />Custom:</td>
                                                <td>
                                                    <input type="text" name="start<?php echo $i;?>timecustom" value="<?php echo date("H:i:s", strtotime("today 06:00")); ?>" id="start<?php echo $i;?>timecustom" disabled="disabled" onchange="updateResultTimeCustom(this, '<?php echo $i;?>', 'start');" />
                                                    <input type="hidden" name="start<?php echo $i;?>timeval" value="<?php echo date("H:i:s", strtotime("today 06:00")); ?>" id="start<?php echo $i;?>timeval" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date:</th>
                                                <td><input type="radio" name="end<?php echo $i;?>date" value="<?php echo date("Y-m-d"); ?>" checked="checked" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" />Same day</td>
                                                <td><input type="radio" name="end<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+1day")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" />Next day</td>
                                                <td><input type="radio" name="end<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+2days")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" />2 days after</td>
                                                <td><input type="radio" name="end<?php echo $i;?>date" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" />Custom:</td>
                                                <td>
                                                    <input type="text" name="end<?php echo $i;?>datecustom" value="<?php echo date("Y-m-d"); ?>" id="end<?php echo $i;?>datecustom" disabled="disabled" onchange="updateResultDateCustom(this, '<?php echo $i;?>', 'end');" />
                                                    <input type="hidden" name="end<?php echo $i;?>dateval" value="<?php echo date("Y-m-d"); ?>" id="end<?php echo $i;?>dateval" />
                                                </td>
                                                <td rowspan="2">Resulting time stamp: <input type="text" name="end<?php echo $i;?>result" value="<?php echo date("Y-m-d H:i:s", strtotime("today 18:00")); ?>" readonly="readonly" id="end<?php echo $i;?>result" /></td>
                                            </tr>
                                            <tr>
                                                <th>Time:</th>
                                                <td><input type="radio" name="end<?php echo $i;?>time" value="09:30:00" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" />Morning break</td>
                                                <td><input type="radio" name="end<?php echo $i;?>time" value="12:00:00" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" />Noon</td>
                                                <td><input type="radio" name="end<?php echo $i;?>time" value="18:00:00" checked="checked" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" />Evening</td>
                                                <td><input type="radio" name="end<?php echo $i;?>time" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" />Custom:</td>
                                                <td>
                                                    <input type="text" name="end<?php echo $i;?>timecustom" value="<?php echo date("H:i:s", strtotime("today 18:00")); ?>" id="end<?php echo $i;?>timecustom" disabled="disabled" onchange="updateResultTimeCustom(this, '<?php echo $i;?>', 'end');" />
                                                    <input type="hidden" name="end<?php echo $i;?>timeval" value="<?php echo date("H:i:s", strtotime("today 18:00")); ?>" id="end<?php echo $i;?>timeval" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td><label><?php lang_echo("ticDispUntil");?>:</label></td>
                            </tr>
                        </table>
                    </fieldset>
<?php } ?>
                </form>
<?php }
//----DELETE/DELETE2--------------------------------------------------------------------------------------------------------------------------------
else if (($view == "delete") || ($view == "delete2")) { ?>
                <div id="tickerDeleteTop">
                    <form id="tickerDeleteForm" action="?component=tickers<?php if ($view == "delete2") echo "&view=deleted"; ?>" method="post">
                        <div id="tickerDeleteButtonBar">
                            <input type="hidden" name="postview" value="unset" id="postview" />
                            <input type="submit" value="<?php lang_echo("ticYesReallyDelete");?>" onclick="document.getElementById('postview').value = '<?php echo ($view == "delete") ? "recycleYes" : "deletePermYes"; ?>';" />
                            <input type="submit" value="<?php lang_echo("ticNoDontDelete");?>" />
                        </div>
                        <?php lang_echo(($view == "delete") ? "ticMoveToTrash?" : "ticDeletePermanently?"); ?> 
                        <input type="hidden" name="delcount" value="<?php echo $delcount; ?>" />
<?php for ($i = 0; $i < $delcount; $i++) echo "                        <input type=\"hidden\" name=\"id$i\" value=\"$delid[$i]\" />";?>
                    </form>
                </div>
                <fieldset id="tickerDeleteList"><legend><?php lang_echo("ticTickersToDelete");?></legend>
                    <table id="tickersHead" summary="" border="0" cellpadding="0" cellspacing="0">
                        <tr><th class="tName"><?php lang_echo("ticCaption");?></th><th class="tContent"><?php lang_echo("ticContent");?></th><th class="tFrom"><?php lang_echo("ticDispFrom");?></th><th class="tUntil"><?php lang_echo("ticDispUntil");?></th></tr>
                    </table>
                    <div id="tickersDiv">
                        <table id="tickers">
<?php tickermanOutputList("toDelete", 32, 0);?>
                        </table>
                    </div>
                </fieldset>
<?php } 
//----DELETED: TRASH BIN----------------------------------------------------------------------------------------------------------------------------
else if ($view == "deleted") { ?>
                <fieldset id="tickerDeleted"><legend><?php lang_echo("ticTrashBin");?></legend>
                    <table id="tickersHead" summary="" border="0" cellpadding="0" cellspacing="0">
                        <tr><th class="tName"><?php lang_echo("ticCaption");?></th><th class="tContent"><?php lang_echo("ticContent");?></th><th class="tFrom"><?php lang_echo("ticDispFrom");?></th><th class="tUntil"><?php lang_echo("ticDispUntil");?></th><th class="tEdit" title="<?php lang_echo("ticEdit");?>"><?php lang_echo("ticEditShort");?></th><th class="tRestore" title="<?php lang_echo("ticRestore");?>"><?php lang_echo("ticRestoreShort");?></th><th class="tDelete" title="<?php lang_echo("ticDelete2");?>"><?php lang_echo("ticDelete2Short");?></th><th class="tCheck"></th></tr>
                    </table>
                    <form id="tickerListForm" action="?component=tickers" method="post">
                        <div id="tickersDiv">
                            <table id="tickers">
<?php tickermanOutputList("deleted", 32, 2);?> 
                            </table>
                        </div>
                        <div id="tickerListButtons">
                            <input type="hidden" name="postview" value="unset" id="postview" />
                            <input type="submit" value="<?php lang_echo("ticEditSelected");?>" onclick="this.form.action = './?component=tickers&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("ticRestoreSelected");?>" onclick="this.form.action = './?component=tickers&view=restore'; document.getElementById('postview').value = 'multiRestore';" />
                            <input type="submit" value="<?php lang_echo("ticDelete2Selected");?>" onclick="this.form.action = './?component=tickers&view=delete2'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                    </form>
                </fieldset>
<?php }
//----EDIT------------------------------------------------------------------------------------------------------------------------------------------
else if ($view == "edit") { ?>
                <form id="tickerEditForm" action="?component=tickers&view=list" method="post">
                    <input type="hidden" name="postview" value="edit" />
                    <div id="tickerEditButtonBar"><input type="submit" value="Save" /></div>
                    <input type="hidden" name="editcount" value="<?php echo $editcount; ?>" />
<?php for ($i = 0; $i < $editcount; $i++) {?>
                    <fieldset class="tickerEdit"><legend><?php lang_echo("ticEditTicker");?></legend>
                        <input type="hidden" name="id<?php echo $i; ?>" value="<?php echo $toEdit[$i]->id?>" />
                        <table class="tickerEditTable" summary="" border="0" cellpadding="0" cellspacing="0">
                            <tr><td><label for="caption<?php echo $i;?>"><?php lang_echo("ticCaption");?>:</label></td><td><input type="text" name="caption<?php echo $i;?>" value="<?php echo $toEdit[$i]->caption; ?>" /></td></tr>
                            <tr><td><label for="content<?php echo $i;?>"><?php lang_echo("ticContent");?>:</label></td><td><textarea name="content<?php echo $i;?>"><?php echo $toEdit[$i]->content; ?></textarea></td></tr>
                            <tr><td><label for="start<?php echo $i;?>"><?php lang_echo("ticDispFrom");?>:</label></td><td><input type="text" name="start<?php echo $i;?>" value="<?php echo $toEdit[$i]->start; ?>" /> <i>Format: <acronym title="Year, 4-digit">YYYY</acronym>-<acronym title="Month, with leading zeroes">MM</acronym>-<acronym title="Day of month, with leading zeroes">DD</acronym> <acronym title="Hour of day (24h), with leading zeroes">HH</acronym>:<acronym title="Minute of hour, with leading zeroes">mm</acronym>:<acronym title="Second of minute, with leading zeroes">ss</acronym></i></td></tr>
                            <tr><td><label for="end<?php echo $i;?>"><?php lang_echo("ticDispUntil");?>:</label></td><td><input type="text" name="end<?php echo $i;?>"  value="<?php echo $toEdit[$i]->end; ?>" /> <i>Format: <acronym title="Year, 4-digit">YYYY</acronym>-<acronym title="Month, with leading zeroes">MM</acronym>-<acronym title="Day of month, with leading zeroes">DD</acronym> <acronym title="Hour of day (24h), with leading zeroes">HH</acronym>:<acronym title="Minute of hour, with leading zeroes">mm</acronym>:<acronym title="Second of minute, with leading zeroes">ss</acronym></i></td></tr>
<?php if ($toEdit[$i]->deleted) { ?>
                            <tr><td><label for="deleted<?php echo $i;?>"><?php lang_echo("ticDeleted");?>:</label></td><td><input type="checkbox" name="deleted<?php echo $i;?>" checked="checked" title="<?php lang_echo("ticDeletedInfo");?>" /></td></tr>
<?php } ?>
                        </table>
                    </fieldset>
<?php } ?>
                </form>
<?php } ?>
            </div>