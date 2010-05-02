<?php
/**
 * @version     2010-05-02
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
defined("__COMMGR_ADMIN") or die("Include the UsrMgr backend first. [" . __FILE__ . "]");

$buttonbarContent = 
'<table summary="" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>With selected:</td>
            <td><input class="likeLink" type="button" value="Invert selection" onclick="a=document.getElementsByTagName(\'input\');for(i=0;i<a.length;i++){if(a[i].type==\'checkbox\'&&a[i].name!=\'\')a[i].checked=!a[i].checked;}" /></td>
            <td><input class="likeLink" type="button" value="Toggle enabled/disabled" onclick="doSubmit(\'toggleActive\', \'\');" /></td>
            <td><input class="likeLink" type="button" value="Edit" onclick="doSubmit(\'\', \'edit\');" /></td>
            <td><input class="likeLink" type="button" value="Duplicate" onclick="doSubmit(\'\', \'duplicate\');" /></td>
            <td><input class="likeLink" type="button" value="Delete" onclick="doSubmit(\'\', \'delete\');" /></td>
        </tr>
    </tbody>
</table>';

?>

<form method="post" action="?comID=<?php echo $activeCom->getId(); ?>" id="listForm">
    <input type="hidden" id="postview" name="postview" value="unset" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="unset" />
    <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="comlist" class="rright">
        <tbody>
            <tr class="headingRow">
                <td class="check"><input type="checkbox" title="Select all" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" /></td>
                <td class="enabled" title="Enabled">E</td>
                <td class="name">Component name</td>
                <td class="comname">Component type</td>
                <td class="installedAt">Installation date</td>
                <td class="id">ID</td>
                <td class="duplicate" title="Duplicate">Du</td>
                <td class="delete" title="Delete">Del</td>
            </tr>
<?php foreach (ComMan::$components as $com) {
?>
            <tr id="row_<?php echo $com->getId(); ?>">
                <td class="check"><input type="checkbox" name="check_<?php echo $com->getId(); ?>" value="Yes" title="Select component '<?php echo $com->getDname(); ?>'" /></td>
                <td class="enabled"><?php if (!($com->isAlwaysOn())) { ?><a href="#" onclick="pv.value='toggleActive'; ai.value='<?php echo $com->getId(); ?>'; form.submit();" title="<?php echo (($com->isEnabled()) ? "Disable" : "Enable"); ?> component '<?php echo $com->getDname(); ?>'"><?php if ($com->isEnabled()) lang_echo("genYes"); else lang_echo("genNo"); ?></a><?php } else { ?><span title="This component cannot be disabled.">Yes</span><?php } ?></td>
                <td class="name"><a href="#" onclick="ai.value='<?php echo $com->getId(); ?>'; form.submit();" title="Edit component '<?php echo $com->getDname(); ?>'"><?php echo $com->getDname(); ?></a></td>
                <td class="comname"><?php echo $com->getComName(); ?></td>
                <td class="installedAt"><?php echo $com->getInstalledAt(); ?></td>
                <td class="id"><?php echo $com->getId(); ?></td>
                <td class="duplicate"><?php if (!($com->isOneOfAKind())) { ?><a href="#" onclick="ai.value='<?php echo $com->getId(); ?>'; form.action=form.action+'&task=duplicate'; form.submit();" title="Duplicate component '<?php echo $com->getDname(); ?>'">Du</a><?php } else { ?><span title="This component cannot be duplicated.">--</span><?php } ?></td>
                <td class="delete"><?php if (!($com->isCore())) { ?><a href="#" onclick="ai.value='<?php echo $com->getId(); ?>'; form.action=form.action+'&task=delete'; form.submit();" title="Delete component '<?php echo $com->getDname(); ?>'">Del</a><?php } else { ?><span title="This component cannot be deleted.">--</span><?php } ?></td>
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
        if (a[i].type=='checkbox' && a[i].checked &&a[i].name!='') {
            l = l.concat(a[i].parentNode.parentNode.id.match(/\d+$/));
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