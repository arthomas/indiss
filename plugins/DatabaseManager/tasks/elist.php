<?php
/**
 * @version     2010-10-13
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
            <td><input class="likeLink" type="button" value="Drop" onclick="doSubmitMultiple(\'\', \'tdrop\');" /></td>
            <td><input class="likeLink" type="button" value="Empty" onclick="doSubmitMultiple(\'\', \'tempty\');" /></td>
        </tr>
    </tbody>
</table>';

?>

<form method="post" action="?plugin=<?php echo $this->iname; ?>" id="listForm">
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
                <td class="check"><input type="checkbox" name="check_<?php echo $table["Name"]; ?>" value="Yes" title="Select table '<?php echo $table["Name"]; ?>'" /></td>
<?php     foreach ($entry as $col) { ?>
				<td class="column"><?php echo $col;?></td>
<?php     } ?>
                <td class="edit"><a href="#" title="Edit this entry" onclick="doSubmitSingle('<?php echo $table["Name"]; ?>','tempty','');">E</a></td>
                <td class="drop"><a href="#" title="Drop this entry" onclick="doSubmitSingle('<?php echo $table["Name"]; ?>','tdrop','');">D</a></td>
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

function doSubmitMultiple(postview, task) {
    l = new Array();
    a = document.getElementsByTagName('input');
    for (i=0;i<a.length;i++) {
        if (a[i].type=='checkbox' && a[i].checked && a[i].name!='') {
            l = l.concat(a[i].name.substr(6));
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

function doSubmitSingle(affected, postview, task) {
    ai.value = affected;
    pv.value = postview;
    if (task != '') {
        form.action = form.action + '&task=' + task;
    }
    form.submit();
}
</script>