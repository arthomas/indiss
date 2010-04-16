<?php
/**
 * @version     2010-04-13
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
                <script type="text/javascript">
                function str_width (str) {
                    var rulerSpan = document.getElementById('ruler');
                    rulerSpan.firstChild.nodeValue = str;
                    return rulerSpan.offsetWidth;
                }
                </script>
                <span id="ruler" style="visibility: hidden;">&nbsp;</span>
                <fieldset id="contentList"><legend><?php lang_echo("conExistingPages");?></legend>
                    <form id="contentListForm" action="?component=content" method="post">
                        <div id="contentListButtons">
                            <input type="hidden" name="postview" value="unset" id="postview" />
                            <input type="submit" value="<?php lang_echo("conEditSelected");?>" onclick="this.form.action = './?component=content&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("conDeleteSelected");?>" onclick="this.form.action = './?component=content&view=delete'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                        <table id="contentTable" summary="" border="0" cellpadding="0" cellspacing="0">
                            <thead>
<?php contentmanOutputTableHead(32); ?>
                            </thead>
                            <tbody>
                                <tr class="category"><td colspan="10"><?php lang_echo("conPresentPages");?></td></tr>
<?php contentmanOutputList("present", 32);?>
                                <tr class="category"><td colspan="10"><?php lang_echo("conFuturePages");?></td></tr>
<?php contentmanOutputList("future", 32);?>
                                <tr class="category"><td colspan="10"><?php lang_echo("conPastPages");?></td></tr>
<?php contentmanOutputList("past", 32);?>
                            </tbody>
                            <tfoot>
<?php contentmanOutputTableHead(32); ?>
                            </tfoot>
                        </table>
                        <div id="contentListButtons">
                            <input type="submit" value="<?php lang_echo("conEditSelected");?>" onclick="this.form.action = './?component=content&view=edit'; document.getElementById('postview').value = 'multiEdit';" />
                            <input type="submit" value="<?php lang_echo("conDeleteSelected");?>" onclick="this.form.action = './?component=content&view=delete'; document.getElementById('postview').value = 'multiDelete';" />
                        </div>
                    </form>
                </fieldset>