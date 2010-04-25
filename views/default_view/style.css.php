<?php
/**
 * @version     2010-04-25
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

    define("__MAIN", 1);

    include_once("../../config/config.php");
    include_once("../../includes/database.php");

    header("CONTENT-TYPE: text/css");
    
    $screenWidth        = getValueByName("global_view_options", "screenDimensionX");
    $screenHeight       = getValueByName("global_view_options", "screenDimensionY");
    $topBarHeight       = getValueByName("view_default_options", "topBarHeight");
    $bottomBarHeight    = getValueByName("view_default_options", "bottomBarHeight");
    $leftMainColWidth   = getValueByName("view_default_options", "leftMainColumnWidth");
    
    if (strpos($leftMainColWidth, "%") !== false) {
        $rightMainColWidth = (100 - $leftMainColWidth) . "%";
    } else {
        $rightMainColWidth = ($screenwidth - $leftMainColWidth) . "px";
    }

?>

iframe {
    border: 0;
}

iframe#top {
    position: absolute;
    top: 0;
    left: 0;
    width: <?php echo $screenWidth; ?>px;
    height: <?php echo $topBarHeight; ?>px;
}

iframe#bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    width: <?php echo $screenWidth; ?>px;
    height: <?php echo $bottomBarHeight; ?>px;
}

table#middleTable {
    position: absolute;
    top: <?php echo $topBarHeight; ?>px;
    left: 0;
    width: 100%;
    height: <?php echo ($screenHeight - $topBarHeight - $bottomBarHeight); ?>px;
}

td#leftMainCol {
    width: <?php echo $leftMainColWidth; ?>;
}

td#rightMainCol {
    width: <?php echo $rightMainColWidth; ?>;
}

iframe#left {
    width: 100%;
    height: <?php echo ($screenHeight - $topBarHeight - $bottomBarHeight); ?>px;
}

iframe#right {
    width: 100%;
    height: <?php echo ($screenHeight - $topBarHeight - $bottomBarHeight); ?>px;
}

<?php if (isset($dbconnected)) mysql_close(); ?>