<?php
/**
 * @version     2010-08-17
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
'<table summary="" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>With selected:</td>
            <td><input class="likeLink" type="button" value="Invert selection" onclick="a=document.getElementsByTagName(\'input\');for(i=0;i<a.length;i++){if(a[i].type==\'checkbox\'&&a[i].name!=\'\')a[i].checked=!a[i].checked;}" /></td>
            <!--<td><input class="likeLink" type="button" value="Toggle enabled/disabled" onclick="doSubmit(\'toggleActive\', \'\');" /></td>-->
            <td><input class="likeLink" type="button" value="Edit" onclick="doSubmit(\'\', \'kedit\');" /></td>
            <!--<td><input class="likeLink" type="button" value="Duplicate" onclick="doSubmit(\'\', \'duplicate\');" /></td>-->
            <td><input class="likeLink" type="button" value="Delete" onclick="doSubmit(\'\', \'kdelete\');" /></td>
        </tr>
    </tbody>
</table>';

?>

<form method="post" action="?plugin=<?php echo $this->iname; ?>" id="listForm">
    <input type="hidden" id="postview" name="postview" value="" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="" />
    <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="PluginKindList" class="rright fwTable">
        <tbody>
            <tr class="headingRow">
                <td class="check"><input type="checkbox" title="Select all" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" /></td>
                <td class="pname">Plugin kind</td>
                <td class="pluginVersion">Version</td>
                <td class="compatVersions">Compatibility</td>
                <td class="type">Type</td>
                <td class="oneOfAKind">One of a kind</td>
                <td class="alwaysOn">Always on</td>
                <td class="numInstances">Instances installed / active</td>
                <td class="id">ID</td>
                <td class="delete" title="Delete">D</td>
            </tr>
<?php foreach ($pluginInfo as $plugin) {
    $id = $plugin["id"];
    $pname = $plugin["pName"];
?>
            <tr id="row_<?php echo $id; ?>">
                <td class="check"><input type="checkbox" name="check_<?php echo $id; ?>" value="Yes" title="Select plugin kind '<?php echo $pname; ?>'" /></td>
                <td class="pname"><a href="#" onclick="ai.value='<?php echo $id; ?>'; form.submit();" title="Edit plugin kind '<?php echo $pname; ?>'"><?php echo $pname; ?></a></td>
                <td class="pluginVersion"><?php echo $plugin["pluginVersion"]; ?></td>
                <td class="compatVersions"><?php echo $plugin["minVersion"] . " - " . $plugin["maxVersion"]; ?></td>
                <td class="type"><?php echo $plugin["type"]; ?></td>
                <td class="oneOfAKind"><?php echo Lang::translate( ($plugin["oneOfAKind"]==1) ? "General_Yes" : "General_No" ) ?></td>
                <td class="alwaysOn"><?php echo Lang::translate( ($plugin["alwaysOn"]==1) ? "General_Yes" : "General_No" ) ?></td>
                <td class="numInstances"><?php $c = $ca = 0; foreach ($pluginInstanceInfo as $p) { if ($p["pName"] == $pname) {$c++; if ($p["enabled"]) $ca++;} } echo "$c / $ca" ?></td>
                <td class="id"><?php echo $id; ?></td>
                <td class="delete"><?php if (!($plugin["core"] == 1)) { ?><a href="#" onclick="ai.value='<?php echo $id; ?>'; form.action=form.action+'&task=kdelete'; form.submit();" title="Delete plugin kind '<?php echo $pname; ?>'">D</a><?php } else { ?><span title="This plugin kind cannot be deleted.">--</span><?php } ?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>
    <div class="buttonbar" id="buttonbarBottom"><?php echo $buttonbarContent; ?></div>
    <div style="clear:both;">&nbsp;</div>
</form>

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