<?php
/**
 * @version     2011-03-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2011 Patrick Lehner
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


if (empty($_GET["frame"])) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />
    
    <link rel="stylesheet" type="text/css" href="css/main.css" />
    <link rel="stylesheet" type="text/css" href="views/<?php echo $view; ?>/style.css.php" />
    
    <title><?php echo $sitename; ?></title>
    <!-- INDISS Copyright (c) 2009-2011 Patrick Lehner -->
</head>
<body>
    <iframe id="top" name="top" src="?frame=top"></iframe>
    <table id="middleTable" summary="" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td id="leftMainCol"><iframe id="left" name="left" src="?frame=left"></iframe></td>
            <td id="rightMainCol"><iframe id="right" name="right" src="?frame=right"></iframe></td>
        </tr>
    </table>
    <iframe id="bottom" name="bottom" src="?frame=bottom"></iframe>
</body>
</html>
<?php } else {
    $query = "SELECT `type`,`source` FROM `view_default` WHERE `frame`='" . $_GET["frame"] . "'";
    if ($result = mysql_query($query)) {
        if ($row = mysql_fetch_assoc($result)) {
            if ($row["type"] == "component") {
                $activeCom = ComMan::getCom((int)$row["source"]);
                include($activeCom->getFullPath() . "/main.php");
            }
        }
    }
}?>