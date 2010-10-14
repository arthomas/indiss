<?php
/**
 * @version     2010-10-14
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
class_exists("PluginDatabaseManager") or die("Class 'PluginPluginManager' is unknown [" . __FILE__ . "]");

if (empty($_GET["table"])) {
    echo "Error: No table was selected to display.";
    return;
}

$table = $_GET["table"];
$entries = $db->readTable($table);

if ($entries === false || count($entries) == 0) {
    echo "This table has no entries.";
    return;
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

CSSJSHandler::addStyle(
"div.PluginDatabaseManager div#task_elist form#tableTaskButtons input {
	padding: 4px 5px;
}

div.PluginDatabaseManager div#task_elist form#tableTaskButtons td {
	border: 0 none;
	padding: 0;
	border-left: 1px solid gray;
	-moz-border-radius: 0;
	border-radius: 0;
}

div.PluginDatabaseManager div#task_elist form#tableTaskButtons td:last-child input {
    -moz-border-radius-topright: 8px;           /*Firefox*/
    border-top-right-radius: 8px;               /*Opera/CSS3*/
    -moz-border-radius-bottomright: 8px;        /*Firefox*/
    border-bottom-right-radius: 8px;            /*Opera/CSS3*/
}
"
);

?>

<table style="margin-bottom: 20px;" summary="" border="0" cellpadding="0" cellspacing="0" class="rright rleft fwTable" id="tableTasks">
	<tbody>
		<tr>
			<td>
				Jump to table:
				<form method="post" action="?plugin=<?php echo $this->iname; ?>&task=elist" style="display: inline;">
					<select onchange="this.form.action = this.form.action + '&table=' + this.value; this.form.submit();">
<?php
    $tables = $db->getArrayN($db->q("SHOW TABLES"));
    foreach ($tables as $t) {
        $selected = (strcasecmp($table, $t[0]) == 0) ? ' selected="selected"' : "";
?>
						<option value="<?php echo $t[0];?>"<?php echo $selected;?>><?php echo $t[0];?></option>
<?php 
    }
    unset($t, $tables, $selected); 
?>
					</select>
				</form>
			</td>
			<td style="padding: 0px; width: 200px; border-left: 0 none;">
				<form method="post" action="?plugin=<?php echo $this->iname; ?>&table=<?php echo $table; ?>" id="tableTaskButtons">
    				<table summary="" border="0" cellpadding="0" cellspacing="0">
    					<tbody>
    						<tr>
    							<td><input type="button" value="Edit structure" title="Edit table structure" class="likeLink" /></td>
    							<td><input type="button" value="Insert values" title="Insert values into this table" class="likeLink" /></td>
    						</tr>
    					</tbody>
    				</table>
				</form>
			</td>
		</tr>
	</tbody>
</table>

<form method="post" action="?plugin=<?php echo $this->iname; ?>&table=<?php echo $table; ?>" id="listForm">
    <input type="hidden" id="postview" name="postview" value="" />
    <input type="hidden" id="affectedIDs" name="affectedIDs" value="" />
    <div class="buttonbar" id="buttonbarTop"><?php echo $buttonbarContent; ?></div>
    <table summary="" border="0" cellpadding="0" cellspacing="0" id="EntriesList" class="rright fwTable">
        <tbody>
            <tr class="headingRow">
                <td class="check"><input type="checkbox" title="Select all" onclick="a=document.getElementsByTagName('input');for(i=0;i< a.length;i++){if(a[i].type=='checkbox')a[i].checked=this.checked;}" /></td>
<?php foreach (array_keys($entries[0]) as $col) { ?>
				<td class="column"><?php echo $col; ?></td>
<?php } ?>
                <td class="edit" title="Edit entry">E</td>
                <td class="drop" title="Drop entry">D</td>
            </tr>
<?php  foreach ($entries as $entry) { ?>
            <tr>
                <td class="check"><input type="checkbox" name="check_<?php echo $entry["id"]; ?>" value="Yes" title="Select this entry" /></td>
<?php     foreach ($entry as $col) { ?>
				<td class="column"><?php echo $col;?></td>
<?php     } ?>
                <td class="edit"><a href="#" title="Edit this entry" onclick="doSubmitSingle('<?php echo $entry["id"]; ?>','','eedit');">E</a></td>
                <td class="drop"><a href="#" title="Drop this entry" onclick="if (confirm('Are you sure you want to drop the this entry?\nThis cannot be undone!')) {doSubmitSingle('<?php echo $entry["id"]; ?>','edrop','elist');}">D</a></td>
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