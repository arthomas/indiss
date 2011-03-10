<?php
/**
 * @version     2011-03-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010-2011 Patrick Lehner
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
class_exists("PluginUserManager") or die("Class 'PluginUserManager' is unknown [" . __FILE__ . "]");

//This type hints enable context-completion in most IDEs:
/* @var $db MySQLConnection */
/* @var $log Logger */
/* @var $this PluginUserManager */

$buttonbarContent = 
'<table summary="" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>With selected:</td>
            <td><input class="likeLink" type="button" value="Invert selection" onclick="a=document.getElementsByTagName(\'input\');for(i=0;i<a.length;i++){if(a[i].type==\'checkbox\'&&a[i].name!=\'\')a[i].checked=!a[i].checked;}" /></td>
            <td><input class="likeLink" type="button" value="Toggle active/inactive" onclick="doSubmit(\'toggleActive\', \'\');" /></td>
            <td><input class="likeLink" type="button" value="Edit" onclick="doSubmit(\'\', \'edit\');" /></td>
            <td><input class="likeLink" type="button" value="Delete" onclick="doSubmit(\'\', \'delete\');" /></td>
        </tr>
    </tbody>
</table>';

?>

<form method="post" action="?plugin=<?php echo $this->iname; ?>" id="listForm">
    <input type="hidden" id="postview" name="postview" value="" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="" />
    <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="PluginList" class="rright fwTable">
        <tbody>
            <tr class="headingRow">
                <td class="check"><input type="checkbox" title="Select all" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" /></td>
                <td class="uname">Username</td>
                <td class="fullname">Full name</td>
                <td class="email">Email address</td>
                <td class="createdAt">Creation date</td>
                <td class="ulevel">Level</td>
                <td class="id">ID</td>
                <td class="enabled" title="Active">A</td>
                <td class="delete" title="Delete">D</td>
            </tr>
<?php foreach (User::$users as $user) {
    $id = $user->getId();
    $uname = $user->getUname();
?>
            <tr id="row_<?php echo $id; ?>">
                <td class="check"><input type="checkbox" name="check_<?php echo $id; ?>" value="Yes" title="Select user '<?php echo $uname; ?>'" /></td>
                <td class="uname"><a href="#" onclick="ai.value='<?php echo $id; ?>'; form.submit();" title="Edit user '<?php echo $uname; ?>'"><?php echo $uname; ?></a></td>
                <td class="fullname"><?php echo $user->getFullName(); ?></td>
                <td class="email"><?php echo $user->getEmail(); ?></td>
                <td class="createdAt"><?php echo $user->getCreatedAt(); ?></td>
                <td class="ulevel"><?php echo $user->getLevel(); ?></td>
                <td class="id"><?php echo $id; ?></td>
                <td class="active"><a href="#" onclick="pv.value='toggleActive'; ai.value='<?php echo $id; ?>'; form.submit();" title="<?php echo (($user->isActive()) ? "Deactivate" : "Activate"); ?> user account '<?php echo $uname; ?>'"><?php echo Lang::translate( ($plugin["enabled"]==1) ? "General_Yes" : "General_No" ) ?></a></td>
                <td class="delete"><a href="#" onclick="ai.value='<?php echo $id; ?>'; form.action=form.action+'&task=delete'; form.submit();" title="Delete user '<?php echo $uname; ?>'">D</a></td>
            </tr>
<?php }
unset($user, $id, $uname);
 ?>
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