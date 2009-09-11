<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
?>
<form id="generalSettings" action="./" method="post">
	            <div class="buttonBar" id="topButtonBar"><?php $indent=5; include("modules/buttonBar.php"); unset($indent); ?>
	            </div>
	            <div id="rr" class="mainColumn">
	                blah
	            </div>
	            <div id="ll" class="mainColumn">
	                <fieldset id="screenRes" class="option"><legend>Bildschirmauflösung</legend>
	                    <div class="optionDesc">
	                    </div>
	                    <div class="optionSettings">
	                        Bildschirmauflösung: <input type="text" maxlength="6" size="5" name="screenResX" id="screenResX" /> x <input type="text" maxlength="6" size="5" name="screenResY" id="screenResY" />px
	                    </div>
	                </fieldset>
	            </div>
	            <div class="floatCleaner">&nbsp;</div>
	            <div class="buttonBar" id="bottomButtonBar"><?php $indent=5; include("modules/buttonBar.php"); unset($indent); ?>
	            </div>
            </form>