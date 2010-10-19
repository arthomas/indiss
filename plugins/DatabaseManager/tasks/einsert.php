<?php
/**
 * @version     2010-10-19
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

$table = $_GET["table"];
$cols = $db->getArrayA($db->q("SHOW COLUMNS FROM `$table`"));
if ($cols === false || count($cols) == 0) {
    echo "Error: Could not retrieve column info.";
    return;
}

if (empty($_POST["numEntries"])) {
    $numEntries = 2;
} else {
    $numEntries = $_POST["numEntries"];
}

$buttonbarContent = 
'<table summary="" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>With selected:</td>
            <td><input class="likeLink" type="button" value="Invert selection" onclick="a=document.getElementsByTagName(\'input\');for(i=0;i<a.length;i++){if(a[i].type==\'checkbox\'&&a[i].name!=\'\')a[i].checked=!a[i].checked;}" /></td>
            <td><input class="likeLink" type="button" value="Edit" onclick="doSubmitMultiple(\'\', \'eedit\');" /></td>
            <td><input class="likeLink" type="button" value="Drop" onclick="if (confirm(\'Are you sure you want to drop the selected entries?\\nThis cannot be undone!\')) {doSubmitMultiple(\'edrop\', \'elist\');}" /></td>
        </tr>
    </tbody>
</table>';

?>

<form method="post" action="?plugin=<?php echo $this->iname; ?>&table=<?php echo $table; ?>" id="listForm">
    <input type="hidden" id="postview" name="postview" value="" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="" />
    <div style="float:right; border: 2px solid black; border-bottom: 0 none; -moz-border-radius-topleft: 8px; -moz-border-radius-topright:8px; padding: 2px 5px 0;">
    	Reload form with
    	<select name="numEntries" onchange="form.action = form.action + '&task=einsert'; form.submit();">
<?php foreach (array(1,2,3,4,5,10,15,20,30,50,100) as $i) {?>
			<option value="<?php echo $i;?>"<?php if ($numEntries==$i) echo ' selected="selected"';?>><?php echo $i;?> entries</option>
<?php }?>
    	</select>
    </div>
    <div class="buttonbar" id="buttonbarTop" style="clear: both;"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="InsertList" class="rright fwTable">
        <tbody>
            <tr class="headingRow">
<?php foreach ($cols as $col) {
    if ($col["Key"] == "PRI") $priKey = $col["Field"]; ?>
				<td class="column<?php if ($col["Field"] == $priKey) echo " priKey"?>"><?php echo $col["Field"]; ?><div class="type"><?php echo $col["Type"];?></div></td>
<?php } ?>
            </tr>
<?php  for ($i = 0; $i < $numEntries; $i++) { ?>
            <tr>
<?php     foreach ($cols as $col) {
            $title = ""; 
            if ($col["Field"] == $priKey) $title = "This field is the primary key. ";
            if (array_search("auto_increment", explode(" ", $col["Extra"])) !== false) $title .= "This is an auto-increment field. You should leave it blank."?>
				<td class="column<?php if ($col["Field"] == $priKey) echo " priKey"?>" title="<?php echo $title;?>"><input name="<?php echo $i . "_" . $col["Field"]; ?>" value="" /></td>
<?php     } ?>
            </tr>
<?php } ?>
			<tr>
				<td colspan="<?php echo count($cols);?>" style="padding: 0;"><input type="button" value="Add row" class="likeLink" style=" padding: 3px 5px; width: 100%;" /></td>
			</tr>
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