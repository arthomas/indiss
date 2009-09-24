<?php
/**
 * @version     2009-09-24
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
    
    include_once("../config/config.php");
    include_once("../includes/database.php");
    
    
    unset( $files ); //just to be sure
    $outputdir = $_SERVER["DOCUMENT_ROOT"] . $basepath . getValueByNameD("com_substtable_options", "default_output_dir", "/cli_scripts");
    $files = array_values(preg_grep( "/^Day\d_\d.html$/", scandir($outputdir) ));
    
    //var_dump($files);
    
    if ( empty( $thisfile ) ) {
        $thisfile = $_SERVER["PHP_SELF"];
    }
    
    //set up the auto-reload string
    if ( empty( $files ) ) {
        $reload = getValueByNameD("com_substtable_options", "error_display_time", 10);
        $reload .= "; URL=$thisfile";
        include (__DIR__ . "/substtable_error.php");
    } else {
        $reload = getValueByNameD("com_substtable_options", "display_time", 15) . "; URL=$thisfile?last=";
        if ( isset( $_GET["last"] ) ) {
            if ( !empty( $files[$_GET["last"] + 1] ) ) {
                $reload .= $_GET["last"] + 1;
                $file = file_get_contents( $outputdir . "/" . $files[$_GET["last"] + 1] );
            } else {
                $reload .= -1;
                $file = file_get_contents( $outputdir . "/" . $files[0] );
            }
        } else {
            $reload .= -1;
            $file = file_get_contents( $outputdir . "/" . $files[0] );
        }
        
        $file = str_replace("%RELOAD_STRING%", $reload, $file);
        echo $file;
    }
    

?>