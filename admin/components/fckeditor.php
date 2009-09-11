<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
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

include_once ("../fckeditor/fckeditor.php");

?>

<div id="fckeditor">
                <form action="sampleposteddata.php" method="post" target="_blank">
<?php
$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath   = $basepath . "/fckeditor/" ;
$oFCKeditor->Value      = '<p>This is some <strong>sample text</strong>. You are using <a href="http://www.fckeditor.net/">FCKeditor</a>.</p>' ;
$oFCKeditor->Create() ;
?>
                <br>
                <input type="submit" value="Submit">
            </form>
        </div>