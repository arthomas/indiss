<?php
/**
 * @version     2009-11-06
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      multiple languages support; for installation script
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

    defined("__INSTALL") or die("Restricted access.");

    include("$lang/$lang.php");
    include_once("str_replace_multi.php");
    
    
    
    function lang($lang_content_string) {
        global $_LANG;
        return (isset($_LANG[$lang_content_string])) ? $_LANG[$lang_content_string] : '[' . $lang_content_string . ']';
    }
    
    function lang_echo($lang_content_string) {
        echo lang($lang_content_string);
    }
    
    
    function html_escape_regional_chars($str) {
        $old = array('�','�','�','�','�','�','�');
        $new = array('&Auml;','&Ouml;','&Uuml;','&auml;','&ouml;','&uuml;','&szlig;');
        return str_replace_multi($old, $new, htmlspecialchars($str));
    }

?>