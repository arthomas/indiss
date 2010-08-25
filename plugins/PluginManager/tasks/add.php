<?php
/**
 * @version     2010-08-25
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

<fieldset id="installNewKindBox" class="rright rleft">
    <legend style="font-weight: bold;">Install a new plugin kind</legend>
    <a href="help" style="" class="fieldsetHelpButton">Help</a>
    <form>
        <input type="text" />
    </form>
</fieldset>

<fieldset id="installedKindBox" class="rright rleft">
    <legend style="font-weight: bold;">Install a new instance of a known plugin kind</legend>
    <form method="post" action="?plugin=<?php echo $this->iname; ?>" id="listForm">
        <input type="hidden" id="postview" name="postview" value="unset" />
        <input type="hidden" id="affectedIDs" name="affectedIDs" value="unset" />
        <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
        <table summary="" border="0" cellpadding="0" cellspacing="0" id="PluginKindList" class="fwTable">
            <tbody>
                <tr class="headingRow">
                    <td class="check"><input type="checkbox" title="Select all" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" /></td>
                    <td class="pname">Plugin kind</td>
                    <td class="id">ID</td>
                </tr>
<?php foreach ($pluginInfo as $plugin) {
    $id = $plugin["id"];
    $pname = $plugin["pName"];
?>
                <tr id="row_<?php echo $id; ?>">
                    <td class="check"><input type="checkbox" name="check_<?php echo $id; ?>" value="Yes" title="Select plugin kind '<?php echo $pname; ?>'" /></td>
                    <td class="pname highlightCell"><a href="#" onclick="ai.value='<?php echo $id; ?>'; form.submit();" title="Add a new plugin of kind '<?php echo $pname; ?>'"><?php echo $pname; ?></a></td>
                    <td class="id"><?php echo $id; ?></td>
                </tr>
<?php } ?>
            </tbody>
        </table>
        <div class="buttonbar" id="buttonbarBottom"><?php echo $buttonbarContent; ?></div>
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