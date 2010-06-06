<?php
/**
 * @version     2010-04-25
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
    
    include_once("config/config.php");
    include_once("includes/database.php");
    
    
    unset( $files ); //just to be sure
    $outputdir = $_SERVER["DOCUMENT_ROOT"] . $basepath . getValueByNameD("com_substtable_options", "default_output_dir", "/cli_scripts");
    $files = array_values(preg_grep( "/^Day\d_\d.html$/", scandir($outputdir) ));
    
    //var_dump($files);
    
    
    //set up the auto-reload string
    if (!empty($_GET["view"])) $t[] = "view=" . $_GET["view"];
    if (!empty($_GET["frame"])) $t[] = "frame=" . $_GET["frame"];
    if ( empty( $files ) ) {
        $reload = getValueByNameD("com_substtable_options", "error_display_time", 10);
        $reload .= "; URL=";
        if (!empty($t))
            $reload .= "?" . implode("&", $t);
        include (dirname(__FILE__) . "/substtable_error.php");
        unset($t);
    } else {
        $reload = getValueByNameD("com_substtable_options", "display_time", 10) . "; URL=";
        if ( isset( $_GET["last"] ) ) {
            if ( !empty( $files[$_GET["last"] + 1] ) ) {
                $t[] = $_GET["last"] + 1;
                $file = file_get_contents( $outputdir . "/" . $files[$_GET["last"] + 1] );
            } else {
                $t[] = 0;
                $file = file_get_contents( $outputdir . "/" . $files[0] );
            }
        } else {
            $t[] = 0;
            $file = file_get_contents( $outputdir . "/" . $files[0] );
        }
        
        $reload .= "?" . implode("&", $t);
        unset($t);
        $file = str_replace("%RELOAD_STRING%", $reload, $file);
        echo $file;
    }
    

?>