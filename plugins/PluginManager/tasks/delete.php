<?php
/**
 * @version     2010-09-29
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

$l = explode(",", $_POST["affectedIDs"]);
foreach ($l as $id)
    $m[] = "'" . $pluginInstanceInfo[$id]["dname"] . "'";
$list = implode(", ", $m);

?>

<fieldset id="deleteKindBox" class="rright rleft">
    <legend style="font-weight: bold;">Delete a plugin kind</legend>
    <a href="help" style="" class="fieldsetHelpButton">Help</a>
    <form method="POST" action="?plugin=<?php echo $this->iname; ?>&task=list">
        <input type="hidden" name="postview" id="postview" value="" />
        <input type="hidden" name="affectedIDs" id="affectedIDs" value="<?php echo $_POST["affectedIDs"]; ?>" />
        <div style="float: right;">
            <input type="button" name="yesdelete" value="Yes" onclick="document.getElementById('postview').value='delete';this.form.submit();" /><br />
            <input type="submit" name="nodelete" value="No" />
        </div>
        Do you really want to delete the plugin(s) <?php echo $list; ?>?
    </form>
</fieldset>