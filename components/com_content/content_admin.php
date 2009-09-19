<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
            $query = "INSERT INTO `com_content` (`name`, `url`, `displaytime`, `start`, `end`, `type`, `deleted`)
                        VALUES ";
            $c = 0; //counter for actual number of added pages
            //var_dump($_POST);
            for ($i = 0; $i < $_POST["new_pages"]; $i++) {
                if (!empty($_POST["URL$i"])) {
                    //determine the link type
                    
                    $URL = $_POST["URL$i"];
                    /*$test = preg_match('/^http:\/\/[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,5}/i', $URL);
                    var_dump ($URL, $test);*/
                    if ( preg_match('/^http:\/\/[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,5}/i', $URL) ) {            //TODO: content admin: make the regex for link type regocnition work!!
                        if ( preg_match('/\.(?:bmp|gif|png|jpg|jpeg|svg)$/', $URL) ) {
                            $type = "ExternalImage";
                        } else if ( preg_match('/\.(?:html|html|php|shtml)$/', $URL) ) {
                            $type = "ExternalPage";
                        } else {
                            $type = "ExternalOther";
                        }
                    } else if ( preg_match('/^[a-zA-Z]+:\/\//', $URL) ) {
                        $type = "ignore";
                    } else {
                        if ( preg_match('/\.(?:bmp|gif|png|jpg|jpeg|svg)$/', $URL) ) {
                            $type = "LocalImage";
                        } else if ( preg_match('/\.(?:html|html|php|shtml)$/', $URL) ) {
                            $type = "LocalPage";
                        } else {
                            $type = "LocalOther";
                        }
                    }
                    //var_dump($type);
                    
                    if ( $type != "ignore" ) {
                        if ( $c > 0 )
                            $query .= ",";
                        $query .= "('" . $_POST["name$i"] . "', '" . $_POST["URL$i"] . "', '" . $_POST["disptime$i"] . "', '" . $_POST["start".$i."result"] . "', '" . $_POST["end".$i."result"] . "', '" . $_POST["type$i"] . "', FALSE)";
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
                    $query = "UPDATE `com_content` SET";
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
        
        $now = date($datefmt);
        unset($past, $preset, $future); //just to be sure
        if (isset($rows))
            foreach ($rows as $value) {
                if ($value->deleted)
                   $deleted[] = $value;
                else if ($value->end < $now)
                   $past[] = $value;
                else if ($value->start > $now)
                   $future[] = $value;
                else
                   $present[] = $value;
            }
    }
    
    //$test = "INSERT INTO `infoscreen`.`com_content` (`id`, `name`, `url`, `displaytime`, `start`, `end`, `type`, `deleted`) VALUES (NULL, \'\', \'http://lol.cion\', \'123\', \'2009-09-18 17:43:43\', \'2009-09-25 17:43:45\', \'ExternalOther\', \'0\');";
    //var_dump($test);
    
    
    

    
    
    /*Some functions*/
    function contentmanNavLink($_view, $name) {
        echo ($_view == $GLOBALS["view"]) ? "<span style=\"font-weight: bold;\">$name</span>" : "<a href=\"?component=content&view=$_view\">$name</a>";
    }
    
    function contentmanOutputTableHead ($indent = 0) {
        for ($i = 0; $i < $indent; $i++) $space .= " ";
        $output = 
            "$space<tr>\n" .
            "$space    <th class=\"tName\">" . lang("conName") . "</th>\n" .                                                                    //  <<-----  $_LANG
            "$space    <th class=\"tURL\">" . lang("conURL") . "</th>\n" .                                                                      //  <<-----  $_LANG
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
        for ($i = 0; $i < $indent; $i++) $output .= " ";
        if  (!isset($GLOBALS[$list]))
            echo $output . "<tr class=\"none\"><td colspan=\"9\">". lang("genNone") . "</td></tr>\n";
        else
            foreach ($GLOBALS[$list] as $value) {
                unset($output);
                $output .= "<tr class=\"$list\">" .
                    "<td class=\"tName\">$value->name</td>" .
                    "<td class=\"tURL\">$value->url</td>" .
                    "<td class=\"tType\">" . lang("conType" . $value->type) . "</td>" .
                    "<td class=\"tDispTime\">$value->displaytime s</td>" .
                    "<td class=\"tFrom\">$value->start</td>" .
                    "<td class=\"tUntil\">$value->end</td>";
                switch ($withselect) {
                    case 1:
                        $output .=
                            "<td class=\"tEdit\"><a href=\"?component=content&view=edit&id=$value->id\" title=\"" . lang("conEdit") . "\">" . lang("conEditShort") . "</a></td>" .              //  <<-----  $_LANG
                            "<td class=\"tDelete\"><a href=\"?component=content&view=delete&id=$value->id\" title=\"" . lang("conDelete") . "\">" . lang("conDeleteShort") . "</a></td>" .      //  <<-----  $_LANG
                            "<td class=\"tCheck\"><input type=\"checkbox\" name=\"$value->id\" title=\"" . lang("conSelectMultiple") . "\"></td>";                                              //  <<-----  $_LANG
                        break;
                    case 2:
                        $output .=
                            "<td class=\"tEdit\"><a href=\"?component=content&view=edit&id=$value->id\" title=\"" . lang("conEdit") . "\">" . lang("conEditShort") . "</a></td>" .              //  <<-----  $_LANG
                            "<td class=\"tRestore\"><a href=\"?component=content&view=restore&id=$value->id\" title=\"" . lang("conRestore") . "\">" . lang("conRestoreShort") . "</a></td>" .  //  <<-----  $_LANG
                            "<td class=\"tDelete\"><a href=\"?component=content&view=delete2&id=$value->id\" title=\"" . lang("conDelete2") . "\">" . lang("conDelete2Short") . "</a></td>" .   //  <<-----  $_LANG
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
                    <a href="?component=contentfiles"><?php lang_echo("navContentFilesManager"); ?></a>
                    <?php contentmanNavLink("deleted",   lang("conNavTrash")); ?> 
                    <?php contentmanNavLink("options",   lang("conNavOptions")); ?> 
                </div>
<?php 

//----LIST------------------------------------------------------------------------------------------------------------------------------------------

if ($view == "list") { ?>
                <fieldset id="contentList"><legend><?php lang_echo("conExistingPages");?></legend>
                    <form id="contentListForm" action="?component=content" method="post">
                        <div id="contentListButtons">
                            <input type="hidden" name="postview" value="unset" id="postview" />
                            <input type="submit" value="<?php lang_echo("conEditSelected");?>" onclick="this.form.action = './?component=content&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("conDeleteSelected");?>" onclick="this.form.action = './?component=content&view=delete'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                        <table id="contentTable" summary="" border="0" cellpadding="0" cellspacing="0">
                            <thead>
<?php contentmanOutputTableHead(32); ?>
                            </thead>
                            <tbody>
                                <tr class="category"><td colspan="9"><?php lang_echo("conPastPages");?></td></tr>
<?php contentmanOutputList("past", 32);?>
                                <tr class="category"><td colspan="9"><?php lang_echo("conPresentPages");?></td></tr>
<?php contentmanOutputList("present", 32);?>
                                <tr class="category"><td colspan="9"><?php lang_echo("conFuturePages");?></td></tr>
<?php contentmanOutputList("future", 32);?>
                            </tbody>
                            <tfoot>
<?php contentmanOutputTableHead(32); ?>
                            </tfoot>
                        </table>
                        <div id="contentListButtons">
                            <input type="submit" value="<?php lang_echo("conEditSelected");?>" onclick="this.form.action = './?component=content&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("conDeleteSelected");?>" onclick="this.form.action = './?component=content&view=delete'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                    </form>
                </fieldset>
<?php } 

//----CREATE----------------------------------------------------------------------------------------------------------------------------------------

else if ($view == "create") {
    $new_pages = (isset($_POST["new_pages"])) ? $_POST["new_pages"] : 2 ;
?>
                <div id="contentCreateTop">
                    <form id="contentCreateRestartForm" action="?component=content&view=create" method="post">
                        <?php lang_echo("conReloadCreate1");?> 
                        <select name="new_pages" onchange="this.form.submit();">
<?php foreach (array(1,2,3,4,5,10,15,20,30,40,50) as $value) {
    $selected = ($value == $new_pages) ? " selected=\"selected\"" : "";
    echo "                            <option value=\"$value\"$selected>$value</option>\n";
}
?>
                        </select>
                        <?php lang_echo("conReloadCreate2");?> 
                    </form>
                    <?php lang_echo("conEmptyURLNotice");?> 
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

                function determineType (obj, index) {
                    //have to use  XXX.firstChild.nodeValue  instead of   XXX.innerText   here cause Firefox is being a bitch and doesnt change the display value with that.
                    output = document.getElementById("info" + index);
                    typemem = document.getElementById("type" + index);
                    var temp;
                    if ( obj.value.length == 0 ) {
                        output.firstChild.nodeValue = "This entry will be ignored: empty URL.";
                        typemem.value = "ignore";
                    } else if ( String(obj.value).search(/^http:\/\/[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,5}/) != -1 ) {
                    	if ( String(obj.value).search(/\.(?:bmp|gif|png|jpg|jpeg|svg)$/) != -1 ) {
                            output.firstChild.nodeValue = "This is an external image.";
                            typemem.value = "ExternalImage";
                    	} else if ( String(obj.value).search(/\.(?:html|html|php|shtml)$/) != -1 ) {
                            output.firstChild.nodeValue = "This is an external page.";
                            typemem.value = "ExternalPage";
                    	} else {
                            output.firstChild.nodeValue = "This is an external link (type not recognized).";
                            typemem.value = "ExternalOther";
                    	}
                    } else if ( ((temp = String(obj.value).match(/:\/\/.+/g)) != null) && (temp.length == 1) ) {
                    	output.firstChild.nodeValue = "This entry will be ignored: unsupported external link.";
                    	typemem.value = "ignore";
                    } else {
                    	if ( String(obj.value).search(/\.(?:bmp|gif|png|jpg|jpeg|svg)$/) != -1 ) {
                            output.firstChild.nodeValue = "This is a local image.";
                            typemem.value = "LocalImage";
                        } else if ( String(obj.value).search(/\.(?:html|html|php|shtml)$/) != -1 ) {
                            output.firstChild.nodeValue = "This is a local page.";
                            typemem.value = "LocalPage";
                        } else {
                            output.firstChild.nodeValue = "This is a local link (type not recognized).";
                            typemem.value = "LocalOther";
                        }
                    }
                }

                function checkDispTime (obj, index) {
                	if ( (String(obj.value).search(/^\d+$/) != -1) && (obj.value <= 0) ) {
                	    determineType ( document.getElementById("URL" + index), index);
                	} else {
                		document.getElementById("info" + index).firstChild.nodeValue = "This entry will be ignored: invalid display time.";
                		document.getElementById("type" + index).value = "ignore";
                	}
                }
                
                </script>
                <form id="contentCreateForm" action="?component=content&view=list" method="post">
                    <input type="hidden" name="postview" value="create" />
                    <div id="contentCreateButtonBar"><input type="submit" value="<?php lang_echo("genSave");?>" /></div>
                    <input type="hidden" name="new_pages" value="<?php echo $new_pages; ?>" />
                    <table id="contentCreateContainerTable" summary="" border="0" cellpadding="2" cellspacing="0">
                        <tbody>
<?php for ($i = 0; $i < $new_pages; $i++) {       //TODO: content admin: make it possible to upload files!!!  TODO: content admin: include CKEditor?>
                            <tr><td>
                                <fieldset class="contentCreateBox"><legend><?php lang_echo("conCreatePage");?> <span class="createBoxTypeInfo" id="info<?php echo $i; ?>">This entry will be ignored: empty URL.</span></legend>
                                    <input type="hidden" id="type<?php echo $i;?>" name="type<?php echo $i; ?>" value="ignore" />
                                    <table class="contentCreateTable" summary="" border="0" cellpadding="2" cellspacing="0">
                                        <tbody>
                                            <tr><td><label for="name<?php echo $i;?>"><?php lang_echo("conName");?>:</label></td><td><div class="FileButtons"><input type="button" value="Browse server..." /><input type="button" value="Upload file..." /><input type="button" value="Create file..." /></div><input type="text" class="nameInput" name="name<?php echo $i;?>" /></td></tr>
                                            <tr><td><label for="URL<?php echo $i;?>"><?php lang_echo("conURL");?>:</label></td><td><input type="text" class="URLInput" name="URL<?php echo $i;?>" id="URL<?php echo $i;?>" onchange="determineType(this, <?php echo $i; ?>);" /></td></tr>
                                            <tr><td><label for="disptime<?php echo $i;?>"><?php lang_echo("conDispTime");?>:</label></td><td><input type="text" class="timeInput" name="disptime<?php echo $i;?>" onchange="checkDispTime(this, <?php echo $i; ?>);" value="<?php echo (($time = getValueByName("com_content_options", "default_display_time")) === false) ? "120" : $time; ?>" />s</td></tr>
                                            <tr>
                                                <td class="vertMiddle"><label><?php lang_echo("conDispFrom");?>:</label></td>
                                                <td rowspan="2">
                                                    <table class="contentDateTable" summary="" border="0" cellpadding="0" cellspacing="0">
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
                                                <td class="vertMiddle"><label><?php lang_echo("conDispUntil");?>:</label></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </fieldset>
                            </td></tr>
<?php } ?>
                        </tbody>
                    </table>
                </form>
<?php }

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
                    <table id="contentHead" summary="" border="0" cellpadding="0" cellspacing="0">
                        <tr><th class="tName"><?php lang_echo("conCaption");?></th><th class="tContent"><?php lang_echo("conContent");?></th><th class="tFrom"><?php lang_echo("conDispFrom");?></th><th class="tUntil"><?php lang_echo("conDispUntil");?></th></tr>
                    </table>
                    <div id="contentDiv">
                        <table id="content">
<?php contentmanOutputList("toDelete", 32, 0);?>
                        </table>
                    </div>
                </fieldset>
<?php } 

//----DELETED: TRASH BIN----------------------------------------------------------------------------------------------------------------------------

else if ($view == "deleted") { ?>
                <fieldset id="contentDeleted"><legend><?php lang_echo("conTrashBin");?></legend>
                    <table id="contentHead" summary="" border="0" cellpadding="0" cellspacing="0">
                        <tr><th class="tName"><?php lang_echo("conCaption");?></th><th class="tContent"><?php lang_echo("conContent");?></th><th class="tFrom"><?php lang_echo("conDispFrom");?></th><th class="tUntil"><?php lang_echo("conDispUntil");?></th><th class="tEdit" title="<?php lang_echo("conEdit");?>"><?php lang_echo("conEditShort");?></th><th class="tRestore" title="<?php lang_echo("conRestore");?>"><?php lang_echo("conRestoreShort");?></th><th class="tDelete" title="<?php lang_echo("conDelete2");?>"><?php lang_echo("conDelete2Short");?></th><th class="tCheck"></th></tr>
                    </table>
                    <form id="contentListForm" action="?component=content" method="post">
                        <div id="contentDiv">
                            <table id="content">
<?php contentmanOutputList("deleted", 32, 2);?> 
                            </table>
                        </div>
                        <div id="contentListButtons">
                            <input type="hidden" name="postview" value="unset" id="postview" />
                            <input type="submit" value="<?php lang_echo("conEditSelected");?>" onclick="this.form.action = './?component=content&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("conRestoreSelected");?>" onclick="this.form.action = './?component=content&view=restore'; document.getElementById('postview').value = 'multiRestore';" />
                            <input type="submit" value="<?php lang_echo("conDelete2Selected");?>" onclick="this.form.action = './?component=content&view=delete2'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                    </form>
                </fieldset>
<?php }

//----EDIT------------------------------------------------------------------------------------------------------------------------------------------

else if ($view == "edit") { ?>
                <form id="contentEditForm" action="?component=content&view=list" method="post">
                    <input type="hidden" name="postview" value="edit" />
                    <div id="contentEditButtonBar"><input type="submit" value="Save" /></div>
                    <input type="hidden" name="editcount" value="<?php echo $editcount; ?>" />
<?php for ($i = 0; $i < $editcount; $i++) {?>
                    <fieldset class="contentEdit"><legend><?php lang_echo("conEditPage");?></legend>
                        <input type="hidden" name="id<?php echo $i; ?>" value="<?php echo $toEdit[$i]->id?>" />
                        <table class="contentEditTable" summary="" border="0" cellpadding="0" cellspacing="0">
                            <tr><td><label for="caption<?php echo $i;?>"><?php lang_echo("conCaption");?>:</label></td><td><input type="text" name="caption<?php echo $i;?>" value="<?php echo $toEdit[$i]->caption; ?>" /></td></tr>
                            <tr><td><label for="content<?php echo $i;?>"><?php lang_echo("conContent");?>:</label></td><td><textarea name="content<?php echo $i;?>"><?php echo $toEdit[$i]->content; ?></textarea></td></tr>
                            <tr><td><label for="start<?php echo $i;?>"><?php lang_echo("conDispFrom");?>:</label></td><td><input type="text" name="start<?php echo $i;?>" value="<?php echo $toEdit[$i]->start; ?>" /> <i>Format: <acronym title="Year, 4-digit">YYYY</acronym>-<acronym title="Month, with leading zeroes">MM</acronym>-<acronym title="Day of month, with leading zeroes">DD</acronym> <acronym title="Hour of day (24h), with leading zeroes">HH</acronym>:<acronym title="Minute of hour, with leading zeroes">mm</acronym>:<acronym title="Second of minute, with leading zeroes">ss</acronym></i></td></tr>
                            <tr><td><label for="end<?php echo $i;?>"><?php lang_echo("conDispUntil");?>:</label></td><td><input type="text" name="end<?php echo $i;?>"  value="<?php echo $toEdit[$i]->end; ?>" /> <i>Format: <acronym title="Year, 4-digit">YYYY</acronym>-<acronym title="Month, with leading zeroes">MM</acronym>-<acronym title="Day of month, with leading zeroes">DD</acronym> <acronym title="Hour of day (24h), with leading zeroes">HH</acronym>:<acronym title="Minute of hour, with leading zeroes">mm</acronym>:<acronym title="Second of minute, with leading zeroes">ss</acronym></i></td></tr>
<?php if ($toEdit[$i]->deleted) { ?>
                            <tr><td><label for="deleted<?php echo $i;?>"><?php lang_echo("conDeleted");?>:</label></td><td><input type="checkbox" name="deleted<?php echo $i;?>" checked="checked" title="<?php lang_echo("conDeletedInfo");?>" /></td></tr>
<?php } ?>
                        </table>
                    </fieldset>
<?php } ?>
                </form>
<?php }

else { ?>
            Error: The view you requested is unknown.
<?php } ?>
            </div>