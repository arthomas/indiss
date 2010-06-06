<?php
/**
 * @version     2010-05-19
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
defined("__CONTENT_ADMIN") or die("Include the content manager backend first. [" . __FILE__ . "]");

if ( $create ) {
    $new_pages = (isset($_POST["new_pages"])) ? $_POST["new_pages"] : 2 ;  //this will set $new_pages to the value passed by the form or the default if it isnt passed
}
?>
                <div id="contentCreateTop">
<?php if ( $create ) { ?>                    <form id="contentCreateRestartForm" action="?comID=<?php echo $activeCom->getId();?>&view=create" method="post">
                        <?php lang_echo("conReloadCreate1");?> 
                        <select name="new_pages" onchange="this.form.submit();">
<?php foreach (array(1,2,3,4,5,10,15,20,30,40,50) as $value) {              //here the user can choose if he wants to create more or less fields for new entries
    $selected = ($value == $new_pages) ? " selected=\"selected\"" : "";     //maybe we should make the default value customizable at some point ?
    echo "                            <option value=\"$value\"$selected>$value</option>\n";
}
?>
                        </select>
                        <?php lang_echo("conReloadCreate2");?> 
                    </form><?php } ?>
                    <?php lang_echo("conEmptyURLNotice");?> 
                </div>
                <script type="text/javascript"><?php //TODO: move JS to separate file (or something) ?> 
                var oldMsg = "<?php lang_echo("conIgnore1"); lang_echo("conIgnoreEmptyURL"); ?>";
                var dispError = false;
                        
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
                    if ( String(obj.value).search(/^2\d{3}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1-2]\d|3[0-1])$/i) != -1 ) {
                        document.getElementById(StartOrEnd + Index + "dateval").value = obj.value;
                        updateResult2 ( Index, StartOrEnd );
                    }
                }

                function updateResultTimeCustom ( obj, Index, StartOrEnd ) {
                    if ( String(obj.value).search(/^(?:[0-1]\d|2[0-3])(?::(?:[0-5]\d)){2}$/i) != -1 ) {
                        document.getElementById(StartOrEnd + Index + "timeval").value = obj.value;
                        updateResult2 ( Index, StartOrEnd );
                    }
                }

                function determineType (obj, index) {
                    //have to use  XXX.firstChild.nodeValue  instead of   XXX.innerText   here cause Firefox is being a bitch and doesnt change the display value with that.
                    output = document.getElementById("info" + index);
                    var temp;
                    if ( obj.value.length == 0 ) {
                        output.firstChild.nodeValue = "<?php lang_echo("conIgnore1"); lang_echo("conIgnoreEmptyURL"); ?>";
                    } else if ( String(obj.value).search(/^http:\/\/[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,5}/i) != -1 ) {
                        if ( String(obj.value).search(/\.(?:html|html|php|shtml)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsExternalPage"); ?>";
                        } else if ( String(obj.value).search(/\.(?:bmp|gif|png|jpg|jpeg|svg)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsExternalImage"); ?>";
                        } else if ( String(obj.value).search(/\.(?:pdf)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsExternalPDF"); ?>";
                        } else if ( String(obj.value).search(/\.(?:swf)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsExternalFlash"); ?>";
                        } else {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsExternalOther"); ?>";
                        }
                    } else if ( ((temp = String(obj.value).match(/:\/\/.+/g)) != null) && (temp.length == 1) ) {
                        output.firstChild.nodeValue = "<?php lang_echo("conIgnore1"); lang_echo("conIgnoreUnsuppProt"); ?>";
                    } else {
                        if ( String(obj.value).search(/\.(?:html|html|php|shtml)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsLocalPage"); ?>";
                        } else if ( String(obj.value).search(/\.(?:bmp|gif|png|jpg|jpeg|svg)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsLocalImage"); ?>";
                        } else if ( String(obj.value).search(/\.(?:pdf)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsLocalPDF"); ?>";
                        } else if ( String(obj.value).search(/\.(?:swf)$/i) != -1 ) {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsLocalFlash"); ?>";
                        } else {
                            output.firstChild.nodeValue = "<?php lang_echo("conThisIsLocalOther"); ?>";
                        }
                    }
                }

                function checkDispTime (obj, index) {
                    if ( (String(obj.value).search(/^\d+$/i) != -1) && (obj.value > 0) ) {
                        determineType ( document.getElementById("URL" + index), index);
                    } else {
                        document.getElementById("info" + index).firstChild.nodeValue = "<?php lang_echo("conIgnore1"); lang_echo("conIgnoreDispTime"); ?>";
                    }
                }

                function editorBtnCheck (inout, index) {
                    obj = document.getElementById("URL" + index);
                    output = document.getElementById("info" + index);
                    if ( ( inout == "in" ) && ( !dispError ) ) {
                        if ( ( String(obj.value).search(/\.(?:html|html)$/i) == -1 ) || ( ( String(obj.value).search(/\.(?:html|html)$/i) != -1 ) && ( String(obj.value).search(/^http:\/\/./i) != -1 ) ) ) {
                            dispError = true;
                            oldMsg = output.firstChild.nodeValue;
                            document.getElementById("info" + index).firstChild.nodeValue = "This is not a local HTML file. You cannot edit it.";
                        }
                    } else if ( inout == "out" && dispError ) {
                        dispError = false;
                        output.firstChild.nodeValue = oldMsg;
                    }
                }
                
                </script>
<?php  $item_count = ($create) ? $new_pages : $editcount; //move the two relevant count variables into a common one ?>
                <form id="contentCreateForm" action="?comID=<?php echo $activeCom->getId();?>&view=list" method="post">
                    <input type="hidden" name="postview" value="<?php echo $view; //this will output either 'create' or 'edit' ?>" />
                    <div id="contentCreateButtonBar"><input type="submit" value="<?php lang_echo("genSave");?>" /><input type="button" value="<?php lang_echo("genCancel"); ?>" onclick="window.location.href='index.php?comID=<?php echo $activeCom->getId(); ?>'" /></div>
                    <input type="hidden" name="<?php echo ($create) ? "new_pages" : "editcount" ; ?>" value="<?php echo $item_count; ?>" />
                    <table id="contentCreateContainerTable" summary="" border="0" cellpadding="2" cellspacing="0">
                        <tbody>
<?php for ($i = 0; $i < $item_count; $i++) { ?>
                            <tr><td>
<?php if ($edit) {?>                                <input type="hidden" name="id<?php echo $i; ?>" value="<?php echo $toEdit[$i]->id; ?>" /><?php } ?>
                                <fieldset class="contentCreateBox"><legend><?php lang_echo( ($create) ? "conCreateItem" : "conEditItem" );?> <span class="createBoxTypeInfo" id="info<?php echo $i; ?>"><?php echo ($create) ? html_escape_regional_chars(lang("conIgnore1") . lang("conIgnoreEmptyURL")) : "&nbsp;"; ?></span></legend>
                                    <table class="contentCreateTable" summary="" border="0" cellpadding="2" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td><label for="name<?php echo $i;?>"><?php lang_echo("conName");?>:</label></td>
                                                <td>
                                                    <div class="FileButtons">
                                                        <input type="button" value="<?php lang_echo("conBrowseServer"); ?>" disabled="disabled"/>
                                                        <input type="button" value="<?php lang_echo("conUploadFile"); ?>" onclick="window.open('<?php echo $basepath; ?>/components/com_content/popup_upload_file.php?index=<?php echo $i; ?>', 'Upload File', 'menubar=no,location=no,height=200,width=500,toolbar=no,status=yes,dependent=yes');" />
                                                        <input type="button" value="<?php lang_echo("conOpenEditor"); ?>" onclick="window.open('<?php echo $basepath; ?>/components/com_content/popup_edit_html_file.php?index=<?php echo $i; ?><?php if ($edit) echo "&oldfile=" . $toEdit[$i]->url; ?>', 'Create File', 'menubar=no,location=no,height=600,width=800,toolbar=no,status=yes,dependent=yes');" <?php if ($edit) echo "onmouseover=\"editorBtnCheck('in', $i);\" onmouseout=\"editorBtnCheck('out', $i);\" "; ?>/>
                                                    </div>
                                                    <input type="text" class="nameInput" name="name<?php echo $i;?>" <?php if ($edit) { echo 'value="' . $toEdit[$i]->name . '" '; } ?>/>
                                                </td>
                                            </tr>
                                            <tr><td><label for="URL<?php echo $i;?>"><?php lang_echo("conURL");?>:</label></td><td><input type="text" class="URLInput" name="URL<?php echo $i;?>" id="URL<?php echo $i;?>" onchange="determineType(this, <?php echo $i; ?>);" <?php if ($edit) { echo 'value="' . $toEdit[$i]->url . '" '; } ?>/></td></tr>
                                            <tr><td><label for="disptime<?php echo $i;?>"><?php lang_echo("conDispTime"); ?>:</label></td><td><input type="text" class="timeInput" name="disptime<?php echo $i;?>" onchange="checkDispTime(this, <?php echo $i; ?>);" value="<?php echo ($edit) ? $toEdit[$i]->displaytime : getValueByNameD("com_content_options", "default_display_time", 120); ?>" />s</td></tr>
                                            <tr><td><label for="tags<?php echo $i;?>"><?php lang_echo("conTags"); ?>:</label></td><td><input type="text" class="tagsInput" name="tags<?php echo $i;?>" value="<?php echo (($edit) ? $toEdit[$i]->tags : "default"); ?>" /></td></tr>
                                            <tr>
                                                <td class="vertMiddle"><label><?php lang_echo("conDispFrom");?>:</label></td>
                                                <td rowspan="2">
                                                    <table class="contentDateTable" summary="" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <th><?php lang_echo("genDate"); ?>:</th>
                                                                <td><input type="radio" name="start<?php echo $i;?>date" value="<?php echo date("Y-m-d"); ?>" <?php if ($create) { echo 'checked="checked" '; }?>onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" /><?php lang_echo("genToday"); ?></td>
                                                                <td><input type="radio" name="start<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+1day")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" /><?php lang_echo("genTomorrow"); ?></td>
                                                                <td><input type="radio" name="start<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+2days")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" /><?php lang_echo("genInTwoDays"); ?></td>
                                                                <td><input type="radio" name="start<?php echo $i;?>date" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'date');" <?php if ($edit) { echo 'checked="checked" '; } ?>/><label for="start<?php echo $i;?>date"><?php lang_echo("genCustomDate"); ?>:</label></td>
                                                                <td>
                                                                    <input type="text" name="start<?php echo $i;?>datecustom" value="<?php echo ($create) ? date("Y-m-d") : date("Y-m-d", strtotime($toEdit[$i]->start)); ?>" id="start<?php echo $i;?>datecustom" <?php if ($create) { echo 'disabled="disabled" '; } ?>onchange="updateResultDateCustom(this, '<?php echo $i;?>', 'start');" />
                                                                    <input type="hidden" name="start<?php echo $i;?>dateval" value="<?php echo ($create) ? date("Y-m-d") : date("Y-m-d", strtotime($toEdit[$i]->start)); ?>" id="start<?php echo $i;?>dateval" />
                                                                </td>
                                                                <td rowspan="2"><?php lang_echo("genResultingTimeStamp"); ?>: <input type="text" name="start<?php echo $i;?>result" value="<?php echo date("Y-m-d H:i:s", strtotime( ($create) ? "today 06:00" : $toEdit[$i]->start)); ?>" readonly="readonly" id="start<?php echo $i;?>result" /></td>
                                                            </tr>
                                                            <tr>
                                                                <th><?php lang_echo("genTime"); ?>:</th>
                                                                <td><input type="radio" name="start<?php echo $i;?>time" value="06:00:00" <?php if ($create) { echo 'checked="checked" '; }?>onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');" /><?php lang_echo("genMorning"); ?></td>
                                                                <td><input type="radio" name="start<?php echo $i;?>time" value="09:30:00" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');" /><?php lang_echo("genMorningBreak"); ?></td>
                                                                <td><input type="radio" name="start<?php echo $i;?>time" value="12:00:00" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');" /><?php lang_echo("genNoon"); ?></td>
                                                                <td><input type="radio" name="start<?php echo $i;?>time" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'start', 'time');"  <?php if ($edit) { echo 'checked="checked" '; } ?>/><?php lang_echo("genCustomTime"); ?>:</td>
                                                                <td>
                                                                    <input type="text" name="start<?php echo $i;?>timecustom" value="<?php echo ($create) ? date("H:i:s", strtotime("today 06:00")) : date("H:i:s", strtotime($toEdit[$i]->start)); ?>" id="start<?php echo $i;?>timecustom" <?php if ($create) { echo 'disabled="disabled" '; } ?>onchange="updateResultTimeCustom(this, '<?php echo $i;?>', 'start');" />
                                                                    <input type="hidden" name="start<?php echo $i;?>timeval" value="<?php echo ($create) ? date("H:i:s", strtotime("today 06:00")) : date("H:i:s", strtotime($toEdit[$i]->start)); ?>" id="start<?php echo $i;?>timeval" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th><?php lang_echo("genDate"); ?>:</th>
                                                                <td><input type="radio" name="end<?php echo $i;?>date" value="<?php echo date("Y-m-d"); ?>" <?php if ($create) { echo 'checked="checked" '; }?>onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" /><?php lang_echo("genToday"); ?></td>
                                                                <td><input type="radio" name="end<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+1day")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" /><?php lang_echo("genTomorrow"); ?></td>
                                                                <td><input type="radio" name="end<?php echo $i;?>date" value="<?php echo date("Y-m-d", strtotime("+2days")); ?>" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" /><?php lang_echo("genInTwoDays"); ?></td>
                                                                <td><input type="radio" name="end<?php echo $i;?>date" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'date');" <?php if ($edit) { echo 'checked="checked" '; } ?>/><?php lang_echo("genCustomDate"); ?>:</td>
                                                                <td>
                                                                    <input type="text" name="end<?php echo $i;?>datecustom" value="<?php echo ($create) ? date("Y-m-d") : date("Y-m-d", strtotime($toEdit[$i]->end)); ?>" id="end<?php echo $i;?>datecustom" <?php if ($create) { echo 'disabled="disabled" '; } ?>onchange="updateResultDateCustom(this, '<?php echo $i;?>', 'end');" />
                                                                    <input type="hidden" name="end<?php echo $i;?>dateval" value="<?php echo ($create) ? date("Y-m-d") : date("Y-m-d", strtotime($toEdit[$i]->end)); ?>" id="end<?php echo $i;?>dateval" />
                                                                </td>
                                                                <td rowspan="2"><?php lang_echo("genResultingTimeStamp"); ?>: <input type="text" name="end<?php echo $i;?>result" value="<?php echo date("Y-m-d H:i:s", strtotime( ($create) ? "today 18:00" : $toEdit[$i]->end)); ?>" readonly="readonly" id="end<?php echo $i;?>result" /></td>
                                                            </tr>
                                                            <tr>
                                                                <th><?php lang_echo("genTime"); ?>:</th>
                                                                <td><input type="radio" name="end<?php echo $i;?>time" value="09:30:00" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" /><?php lang_echo("genMorningBreak"); ?></td>
                                                                <td><input type="radio" name="end<?php echo $i;?>time" value="12:00:00" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" /><?php lang_echo("genNoon"); ?></td>
                                                                <td><input type="radio" name="end<?php echo $i;?>time" value="18:00:00" <?php if ($create) { echo 'checked="checked" '; }?>onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" /><?php lang_echo("genEvening"); ?></td>
                                                                <td><input type="radio" name="end<?php echo $i;?>time" value="custom" onclick="updateResult(this, '<?php echo $i;?>', 'end', 'time');" <?php if ($edit) { echo 'checked="checked" '; } ?>/><?php lang_echo("genCustomTime"); ?>:</td>
                                                                <td>
                                                                    <input type="text" name="end<?php echo $i;?>timecustom" value="<?php echo ($create) ? date("H:i:s", strtotime("today 06:00")) : date("H:i:s", strtotime($toEdit[$i]->end)); ?>" id="end<?php echo $i;?>timecustom" <?php if ($create) { echo 'disabled="disabled" '; } ?>onchange="updateResultTimeCustom(this, '<?php echo $i;?>', 'end');" />
                                                                    <input type="hidden" name="end<?php echo $i;?>timeval" value="<?php echo ($create) ? date("H:i:s", strtotime("today 06:00")) : date("H:i:s", strtotime($toEdit[$i]->end)); ?>" id="end<?php echo $i;?>timeval" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="vertMiddle"><label><?php lang_echo("conDispUntil");?>:</label></td>
                                            </tr>
<?php if ($edit && $toEdit[$i]->deleted) { ?>
                                            <tr>
                                                <td><label for="deleted<?php echo $i;?>"><?php lang_echo("conDeleted");?>:</label></td><td><input type="checkbox" name="deleted<?php echo $i;?>" checked="checked" title="<?php lang_echo("conDeletedInfo");?>" /><input type="hidden" name="wasdeleted<?php echo $i;?>" value="yes" /></td>
                                            </tr>
<?php } ?>
                                            <tr>
                                                <td><label for="enabled<?php echo $i;?>"><?php lang_echo("conEnabled");?>:</label></td><td><input type="checkbox" name="enabled<?php echo $i;?>" <?php if ($create || ($edit && $toEdit[$i]->enabled)) echo 'checked="checked" '; ?>/></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </fieldset>
                            </td></tr>
<?php } ?>
                        </tbody>
                    </table>
                </form>