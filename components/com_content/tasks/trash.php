<?php
/**
 * @version     2010-05-19
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
defined("__CONTENT_ADMIN") or die("Include the content manager backend first. [" . __FILE__ . "]");

?>
                <fieldset id="contentDeleted"><legend><?php lang_echo("conTrashBin");?></legend>
                    <form id="contentListForm" action="?comID=<?php echo $activeCom->getId(); ?>" method="post">
                        <input type="hidden" name="postview" value="unset" id="postview" />
                        <div id="contentListButtons">
                            <input type="submit" value="<?php lang_echo("conEditSelected");?>" onclick="this.form.action = this.form.action + '&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("conRestoreSelected");?>" onclick="this.form.action = this.form.action + '&view=restore'; document.getElementById('postview').value = 'multiRestore';" />
                            <input type="submit" value="<?php lang_echo("conDelete2Selected");?>" onclick="this.form.action = this.form.action + '&view=delete2'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                        <table id="contentTable">
                            <thead>
                                <tr><th class="tName"><?php lang_echo("conName");?></th><th class="tURL"><?php lang_echo("conURL");?></th><th class="tTags"><?php lang_echo("conTags");?></th><th class="tType"><?php lang_echo("conType");?></th><th class="tDispTime"><?php lang_echo("conDispTime");?></th><th class="tFrom"><?php lang_echo("conDispFrom");?></th><th class="tUntil"><?php lang_echo("conDispUntil");?></th><th class="tEdit" title="<?php lang_echo("conEdit");?>"><?php lang_echo("conEditShort");?></th><th class="tRestore" title="<?php lang_echo("conRestore");?>"><?php lang_echo("conRestoreShort");?></th><th class="tDelete" title="<?php lang_echo("conDelete2");?>"><?php lang_echo("conDelete2Short");?></th><th class="tCheck"></th></tr>
                            </thead>
                            <tbody>
<?php contentmanOutputList("deleted", 32, 2);?> 
                            </tbody>
                        </table>
                        <div id="contentListButtons">
                            <input type="submit" value="<?php lang_echo("conEditSelected");?>" onclick="this.form.action = this.form.action + '&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("conRestoreSelected");?>" onclick="this.form.action = this.form.action + '&view=restore'; document.getElementById('postview').value = 'multiRestore';" />
                            <input type="submit" value="<?php lang_echo("conDelete2Selected");?>" onclick="this.form.action = this.form.action + '&view=delete2'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                    </form>
                </fieldset>