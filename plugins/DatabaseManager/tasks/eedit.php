<?php
/**
 * @version     2010-10-16
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

//note that this file's scope is within a function! it is being included from within PluginDatabaseManager::outputAdmin()

//This type hints enable context-completion in most IDEs:
/* @var $db MySQLConnection */
/* @var $log Logger */
 
defined("__MAIN") or die("Restricted access.");
class_exists("PluginDatabaseManager") or die("Class 'PluginDatabaseManager' is unknown [" . __FILE__ . "]");

if (empty($_GET["table"])) {
    echo "Error: No table was selected to display.";
    return;
}

$l = explode(",", $_POST["affectedIDs"]);
if (count($l) < 1) {
    echo "Error: No entries were selected to be edited.";
    return;
}

$table = $_GET["table"];
$entries = $db->getArrayA($db->q("SELECT * FROM `$table` WHERE `id` IN ({$_POST["affectedIDs"]})"));

if ($entries === false || count($entries) == 0) {
    echo "Error: No entries could be retrieved from the database.";
    return;
}

$cols = $db->getArrayA($db->q("SHOW COLUMNS FROM `$table`"));
if ($cols === false || count($cols) == 0) {
    echo "Error: Could not retrieve column info.";
    return;
}

$buttonbarContent = 
'<table summary="" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td><input class="likeLink" type="button" value="Cancel" onclick="window.location.href = ' . "'?plugin={$this->iname}&table=$table&task=elist'" . ';" /></td>
            <td><input class="likeLink" type="submit" value="Save changes" /></td>
        </tr>
    </tbody>
</table>';

CSSJSHandler::addStyle(
""
);

?>

<form method="post" action="?plugin=<?php echo $this->iname; ?>&table=<?php echo $table; ?>&task=elist" id="listForm">
    <input type="hidden" id="postview" name="postview" value="eedit" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="<?php echo $_POST["affectedIDs"];?>" />
    <input type="hidden" id="table" name="table" value="<?php echo $_GET["table"]; ?>" />
    <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="EntriesEditList" class="rright fwTable">
        <tbody>
            <tr class="headingRow">
<?php foreach (array_keys($entries[0]) as $col) { ?>
				<td class="column"><?php echo $col; ?></td>
<?php } ?>
            </tr>
            <tr class="typeRow">
<?php foreach ($cols as $col) {?>
				<td class="column type"><?php echo $col["Type"]; ?></td>
<?php } ?>
            </tr>
<?php  foreach ($entries as $entry) { ?>
            <tr>
<?php     foreach ($entry as $key => $val) { ?>
				<td class="column"><input name="<?php echo $entry["id"] . "_" . $key; ?>" value="<?php echo $val;?>" /></td>
<?php     } ?>
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
</script>