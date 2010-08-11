<?php
/**
 * @version     2010-08-11
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
            <td><input class="likeLink" type="button" value="Toggle enabled/disabled" onclick="doSubmit(\'toggleActive\', \'\');" /></td>
            <td><input class="likeLink" type="button" value="Edit" onclick="doSubmit(\'\', \'edit\');" /></td>
            <!--<td><input class="likeLink" type="button" value="Duplicate" onclick="doSubmit(\'\', \'duplicate\');" /></td>-->
            <td><input class="likeLink" type="button" value="Delete" onclick="doSubmit(\'\', \'delete\');" /></td>
        </tr>
    </tbody>
</table>';

?>

<form method="post" action="?plugin=<?php echo $this->iname; ?>" id="listForm">
    <input type="hidden" id="postview" name="postview" value="unset" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="unset" />
    <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="PluginList" class="rright">
        <tbody>
            <tr class="headingRow">
                <td class="check"><input type="checkbox" title="Select all" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" /></td>
                <td class="dname">Plugin name</td>
                <td class="iname">Plugin name</td>
                <td class="pname">Plugin kind</td>
                <td class="installedAt">Installation date</td>
                <td class="id">ID</td>
                <td class="enabled" title="Enabled">E</td>
                <td class="delete" title="Delete">D</td>
            </tr>
<?php foreach ($pluginInstanceInfo as $plugin) {
    $id = $plugin["id"];
    $dname = $plugin["dname"];
?>
            <tr id="row_<?php echo $id; ?>">
                <td class="check"><input type="checkbox" name="check_<?php echo $id; ?>" value="Yes" title="Select plugin '<?php echo $dname; ?>'" /></td>
                <td class="dname"><a href="#" onclick="ai.value='<?php echo $id; ?>'; form.submit();" title="Edit component '<?php echo $dname; ?>'"><?php echo $dname; ?></a></td>
                <td class="iname"><?php echo $plugin["iname"]; ?></td>
                <td class="pname"><?php echo $plugin["pName"]; ?></td>
                <td class="installedAt"><?php echo $plugin["installedAt"]; ?></td>
                <td class="id"><?php echo $id; ?></td>
                <td class="enabled"><?php if (!($pluginInfo[$plugin["pName"]]["alwaysOn"] == 1)) { ?><a href="#" onclick="pv.value='toggleActive'; ai.value='<?php echo $id; ?>'; form.submit();" title="<?php echo (($plugin["enabled"]==1) ? "Disable" : "Enable"); ?> plugin '<?php echo $dname; ?>'"><?php echo Lang::translate( ($plugin["enabled"]==1) ? "General_Yes" : "General_No" ) ?></a><?php } else { ?><span title="This plugin cannot be disabled.">Yes</span><?php } ?></td>
                <td class="delete"><?php if (!($pluginInfo[$plugin["pName"]]["core"] == 1)) { ?><a href="#" onclick="ai.value='<?php echo $id; ?>'; form.action=form.action+'&task=delete'; form.submit();" title="Delete plugin '<?php echo $dname; ?>'">D</a><?php } else { ?><span title="This plugin cannot be deleted.">--</span><?php } ?></td>
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