<?php
/**
 * @version     2010-09-24
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

//note that this file's scope is within a function! it is being included from within PluginPluginManager::outputAdmin()
 
defined("__MAIN") or die("Restricted access.");
class_exists("PluginPluginManager") or die("Class 'PluginPluginManager' is unknown [" . __FILE__ . "]");

$buttonbarContent = 
'<table summary="" border="0" cellpadding="0" cellspacing="0" class="buttonBarTable">
    <tbody>
        <tr>
            <td><input class="likeLink" type="button" value="Add plugins of the selected kinds" /></td>
        </tr>
    </tbody>
</table>';
?>

<fieldset class="addInstanceBox" class="rright rleft">
    <legend style="font-weight: bold;">Add a new instance</legend>
    <a href="help" class="fieldsetHelpButton" style="margin-right: 10px;">Help</a>
    <div id="installedKindId" style="margin: 0 10px 10px;">
        <p>Please enter a descriptive name and/or an internal name for the new plugin instance below. If you leave a field empty, it will be automatically filled.</p>
        <p><span>Descriptive name:</span> May contain all kinds of characters and need not be unique. Will be auto-filled if empty.</p>
        <p><span>Internal name:</span> May contain only alphanumerical characters (a-z, A-Z, 0-9), dashes (-) and underscores (_); may not contain spaces or special characters. Must be unique.</p>
    </div>
    <form method="post" action="?plugin=<?php echo $this->iname; ?>" id="listForm">
        <input type="hidden" id="postview" name="postview" value="addInstance" />
        <input type="hidden" id="affectedIDs" name="affectedIDs" value="<?php echo $_POST["affectedIDs"]; ?>" />
        <table summary="" border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td width="200">Plugin kind:</td>
                    <td><?php 
foreach ($pluginInfo as $p) {
    if ($p["id"] == $_POST["affectedIDs"]) {
        echo $p["pName"];
        break;
    }
}?></td>
                </tr>
                <tr>
                    <td>Descriptive name:</td>
                    <td><input type="text" name="dname" /></td>
                </tr>
                <tr>
                    <td>Internal name:</td>
                    <td><input type="text" name="iname" /></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right;"><input type="submit" name="add" value="Add" /></td>
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>

<script type="text/javascript">
pv = document.getElementById("postview");
ai = document.getElementById("affectedIDs");
form = document.getElementById("listForm");

function doSubmit(postview, task) {
    l = new Array();
    a = document.getElementsByTagName('input');
    for (i=0;i<a.length;i++) {
        if (a[i].type=='checkbox' && a[i].checked && a[i].name!='') {
            l = l.concat(a[i].name.match(/\d+$/));
        }
    }
    if (l.length > 0) {
        ai.value = l.join(",");
        pv.value = postview;
        if (task != '') {
            form.action = form.action + '&task=' + task;
        }
        form.submit();
    }
}
</script>