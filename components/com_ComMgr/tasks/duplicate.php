<?php
/**
 * @version     2010-04-27
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

if (empty($_POST["affectedIDs"])) {
    echo "No components were selected to be duplicated.";
} else {
    $IDs = explode(",", $_POST["affectedIDs"]);
    
    unset($coms);
    unset($_IDs);
    foreach ($IDs as $ID) {
        if (($com = ComMan::getCom((int)$ID, true)) !== false) {
            if ($com->isOneOfAKind()) {
                $handler->addMsg("Component manager", "Component '" . $com->getDname() . "' cannot be duplicated", LiveErrorHandler::EK_ERROR);
            } else {
                $coms[] = $com;
                $_IDs[] = $ID;
            }
        } else {
            $handler->addMsg("Component manager", "Could not retrieve component with id '$ID'", LiveErrorHandler::EK_ERROR);
        }
    }
    unset($IDs, $ID);   
}

if (empty($coms)) {
?>
<div>None of the desired components could be retrieved.</div>
<?php } else {
    $buttonbarContent = 
    '<table summary="" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td><input class="likeLink" type="button" value="Duplicate" onclick="this.form.submit();" /></td>
            </tr>
        </tbody>
    </table>';
?>
<form method="post" action="?comID=<?php echo $activeCom->getId(); ?>" id="duplicateForm">
    <input type="hidden" id="postview" name="postview" value="duplicate" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="<?php echo implode(",", $_IDs);?>" />
    <div class="buttonbar" id="buttonbarTop" style="float: right;"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="comlist" class="rleft">
        <tbody>
            <tr class="headingRow">
                <td class="olddname">Component name</td>
                <td class="oldiname">Internal name</td>
                <td class="newdname"><acronym title="A descriptive name for the duplicate.">Duplicate name</acronym></td>
                <td class="newiname"><acronym title="The internal name for the duplicate. Must be unique and may only contain letters, numbers, dashes (-) and underscores (_); no spaces or fancy characters allowed.">Duplicate internal name</acronym></td>
                <td class="check"><input type="checkbox" title="Enable all duplicates" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" checked="checked" /></td>
            </tr>
<?php foreach ($coms as $com) {?>
            <tr id="row_<?php echo $com->getId(); ?>">
                <td class="olddname"><?php echo $com->getDname(); ?></td>
                <td class="oldiname"><?php echo $com->getIname(); ?></td>
                <td class="newdname"><input type="text" name="newdname_<?php echo $com->getId(); ?>" id="newdname_<?php echo $com->getId(); ?>" maxlength="255" /></td>
                <td class="newiname"><input type="text" name="newiname_<?php echo $com->getId(); ?>" id="newiname_<?php echo $com->getId(); ?>" maxlength="255" /></td>
                <td class="check"><input type="checkbox" name="enable_<?php echo $com->getId(); ?>" value="Yes" checked="checked" title="Enable the duplicate of '<?php echo $com->getDname(); ?>'" /></td>
            </tr>
<?php } ?>
        </tbody>
    </table>
    <div class="buttonbar" id="buttonbarBottom" style="float:right;"><?php echo $buttonbarContent; ?></div>
    <div style="clear:both;">&nbsp;</div>
</form>
<?php }
            
?>