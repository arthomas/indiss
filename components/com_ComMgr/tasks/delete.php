<?php
/**
 * @version     2010-05-14
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
    echo "No components were selected to be deleted.";
} else {
    $IDs = explode(",", $_POST["affectedIDs"]);
    
    unset($coms);
    unset($_IDs);
    foreach ($IDs as $ID) {
        if (($com = ComMan::getCom((int)$ID, true)) !== false) {
            if ($com->isCore()) {
                $handler->addMsg("Component manager", "Component '" . $com->getDname() . "' cannot be deleted", LiveErrorHandler::EK_ERROR);
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
<?php } else { ?>
<form method="post" action="?comID=<?php echo $activeCom->getId(); ?>" id="deleteForm">
    <input type="hidden" id="postview" name="postview" value="delete" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="unset" />
    <div id="delquestion">
        <input type="button" value="Yes, delete them" />
        <input type="button" value="No, cancel deletion" />
        Are you sure you want to permanently delete all components listed below? This will remove
        the component from the database and delete all its files from the file system. This
        operation cannot be undone!
    </div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="comlist" class="rleft rright">
        <tbody>
            <tr class="headingRow">
                <td class="deldname">Component name</td>
                <td class="deliname">Internal name</td>
                <td class="delid">ID</td>
            </tr>
<?php foreach ($coms as $com) {?>
            <tr id="row_<?php echo $com->getId(); ?>">
                <td class="deldname"><?php echo $com->getDname(); ?></td>
                <td class="deliname"><?php echo $com->getIname(); ?></td>
                <td class="delid"><?php echo $com->getId();?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>
</form>
<?php } ?>