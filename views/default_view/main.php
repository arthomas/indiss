<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
    
    $topsrc = "modules/top.php";
    $bottomsrc = "modules/bottom.php";
    $leftsrc = "modules/left.php";
    $rightsrc = "cli_scripts/Day0_0.html";
    
?>
<iframe id="top" name="top" src="<?php echo $topsrc;?>"></iframe>
<table id="middleTable" summary="" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td id="leftMainCol"><iframe id="left" name="left" src="<?php echo $leftsrc;?>"></iframe></td>
        <td id="rightMainCol"><iframe id="right" name="right" src="<?php echo $rightsrc;?>"></iframe></td>
    </tr>
</table>
<iframe id="bottom" name="bottom" src="<?php echo $bottomsrc;?>"></iframe>