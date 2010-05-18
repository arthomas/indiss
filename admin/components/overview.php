<?php
/**
 * @version     2010-05-18
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
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
<div>Available option pages:</div>
                <ul>
                    <li><strong>Global:</strong>
                        <ul>
                            <li><a href="?component=settings">General settings</a> <i>WIP</i></li>
                            <li><a href="?component=prefsui">Preferences UI</a></li>
                        </ul>
                    </li>
                    <li><strong>Components:</strong>
                        <ul>
<?php foreach (ComMan::$components as $com) { $v = $com->getPath(); ?>
                            <li><?php if ($com->isEnabled() && $com->hasBackend() && !empty($v)) echo "<a href=\"?comID=" . $com->getId() . "\">" ; echo $com->getDname(); if ($com->isEnabled() && !empty($v)) echo "</a>"; ?> <i>(<?php echo $com->getComName(); ?>)</i></li>
<?php } ?>
                        </ul>
                    </li>
                </ul>