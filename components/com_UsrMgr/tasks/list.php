<?php
/**
 * @version     2010-04-11
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
defined("__USRMGR_ADMIN") or die("Include the UsrMgr backend first. [" . __FILE__ . "]");

$buttonbarContent = 
'<table summary="" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>With selected:</td>
            <td><input class="likeLink" type="button" value="Invert selection" onclick="a=document.getElementsByTagName(\'input\');for(i=0;i<a.length;i++){if(a[i].type==\'checkbox\'&&a[i].name!=\'\')a[i].checked=!a[i].checked;}" /></td>
            <td><input class="likeLink" type="button" value="Toggle active" /></td>
            <td><input class="likeLink" type="button" value="Edit" /></td>
            <td><input class="likeLink" type="button" value="Delete" /></td>
        </tr>
    </tbody>
</table>';

?>

<script type="text/javascript">
function doSubmit(postview) {
	
}
</script>

<form method="post" action="?comID=<?php echo $activeCom->getId(); ?>" id="listForm">
    <input type="hidden" id="postview" name="postview" value="" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="" />
    <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="userlist">
        <tbody>
            <tr class="headingRow">
                <td class="check"><input type="checkbox" title="Select all" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" /></td>
                <td class="active" title="Active">A</td>
                <td class="uname">User name</td>
                <td class="fullname">Full name</td>
                <td class="email">Email</td>
                <td class="level">Level</td>
                <td class="id">ID</td>
                <td class="delete">D</td>
            </tr>
<?php foreach (UsrMan::$users as $usr) { ?>
            <tr id="row_<?php echo $usr->getId(); ?>">
                <td class="check"><input type="checkbox" name="check_<?php echo $usr->getId(); ?>" value="Yes" title="Select user '<?php echo $usr->getUname(); ?>'" /></td>
                <td class="active"><a href="#" onclick="pv.value='toggleActive'; ai.value='<?php echo $usr->getId(); ?>'; form.submit();" title="Toggle active/inactive for user '<?php echo $usr->getUname(); ?>'"><?php if ($usr->isActive()) lang_echo("genYes"); else lang_echo("genNo"); ?></a></td>
                <td class="uname"><a href="#" onclick="pv.value='edit'; ai.value='<?php echo $usr->getId(); ?>'; form.submit();" title="Edit user '<?php echo $usr->getUname(); ?>'"><?php echo $usr->getUname(); ?></a></td>
                <td class="fullname"><?php echo $usr->getFullName(); ?></td>
<?php 
$email = $usr->getEmail();
if (!empty($email)) 
    $email_string = sprintf('<a href="mailto:%s" title="Send email to user \'%s\' (%s)">%s</a>', $email, $usr->getUname(), $email, "%s");
else
    $email_string = "%s";
?>
                <td class="email"><?php echo sprintf($email_string, $email); ?></td>
                <td class="level"><?php echo lang(($usr->isAdmin()) ? "usrmanLevelAdmin" : "usrmanLevelUser")?></td>
                <td class="id"><?php echo $usr->getId(); ?></td>
                <td class="delete"><a href="#" onclick="pv.value='edit'; ai.value='<?php echo $usr->getId(); ?>'; form.action=form.action+'&task=delete'; form.submit();" title="Delete user '<?php echo $usr->getUname(); ?>'">D</a></td>
            </tr>
<?php } ?>
        </tbody>
    </table>
    <div class="buttonbar" id="buttonbarBottom"><?php echo $buttonbarContent; ?></div>
</form>

<script type="text/javascript">
pv = document.getElementById("postview");
ai = document.getElementById("affectedIDs");
form = document.getElementById("listForm");
</script>