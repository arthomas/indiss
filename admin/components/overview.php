<?php
/**
 * @version     2010-03-20
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
                            <li><!--<a href="?component=viewman">-->View manager<!--</a>--> <i>n/a</i></li>
                            <li><!--<a href="?component=layout">-->Style and layout<!--</a>--> <i>n/a</i></li>
                            <li><!--<a href="?component=userman">-->User manager<!--</a>--> <i>n/a</i></li>
                            <li><!--<a href="?component=errlog">-->Error log<!--</a>--> <i>n/a</i></li>
                        </ul>
                    </li>
                    <li><strong>Headline:</strong>
                        <ul>
                            <li><!--<a href="?component=headlineman">-->Headline manager<!--</a>--> <i>n/a</i></li>
                        </ul>
                    </li>
                    <li><strong>Tickers:</strong>
                        <ul>
                            <li><a href="?component=tickers">Ticker manager</a> <i>WIP</i></li>
                        </ul>
                    </li>
                    <li><strong>Content:</strong>
                        <ul>
                            <li><a href="?component=content">Content manager</a> <i>WIP</i></li>
                        </ul>
                    </li>
                    <li><strong>Substitution table:</strong>
                        <ul>
                            <li><!--<a href="?component=substtable">-->Substitution table options<!--</a>--> <i>n/a</i></li>
                        </ul>
                    </li>
                    <li><strong>Components:</strong>
                        <ul>
<?php foreach (ComMan::$components as $com) { $v = $com->getPath(); ?>
                            <li><?php if (!empty($v)) echo "<a href=\"?comID=" . $com->getId() . "\">" ; echo $com->getName(); if (!empty($v)) echo "</a>"; ?> <i>(<?php echo $com->getComName(); ?>)</i></li>
<?php } ?>
                        </ul>
                    </li>
                </ul>