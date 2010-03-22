<?php
/**
 * @version     2010-01-07
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
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
    
    $topsrc = "views/default_view/modules/top.php";
    $bottomsrc = "views/default_view/modules/bottom.php";
    $leftsrc = "views/default_view/modules/left.php";
    $rightsrc = "views/default_view/modules/right.php";
    
?>
<iframe id="top" name="top" src="<?php echo $topsrc;?>"></iframe>
<table id="middleTable" summary="" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td id="leftMainCol"><iframe id="left" name="left" src="<?php echo $basepath; ?>/vertretungen/heute/anzeige_heute.html"></iframe></td>
        <td id="rightMainCol"><iframe id="right" name="right" src="<?php echo $basepath; ?>/vertretungen/morgen/anzeige_morgen.html"></iframe></td>
    </tr>
</table>
<iframe id="bottom" name="bottom" src="<?php echo $bottomsrc;?>"></iframe>