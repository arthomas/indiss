<?php
/**
 * @version     2009-12-03
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Installation script - creates necessary database tables and default entries
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

//TODO: install: create installation script

    define("__INSTALL", 1);
    
    $defaultlang = "en";
    
    if ( !empty( $_POST["lang"] ) ) {
        $lang = $_POST["lang"];
    } else {
        $lang = $defaultlang;
    }
    
    include ( "lang/lang.php");
    
    if ( empty( $_GET["step"] ) ) {
        $step = 1;
    } else {
        $step = $_GET["step"];
    }
    
    include ("page" . $step . ".php");
    

?>