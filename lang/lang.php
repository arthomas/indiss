<?php
/**
 * @version     2010-04-20
 * @author      Patrick Lehner
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
    defined("__DIRAWARE") or die("Directory awareness not included [lang.php]");
    
    define("__LANG", 1);
    
    include("languages.php");
    
    $_lang = dirname(__FILE__) . "/$lang"; //create the full path of the language directory
    if (file_exists($_lang)) {
        if (is_dir($_lang)) {
            $langFiles = preg_grep("/\.php/i", scandir("$_lang"));
            if (!empty($langFiles)) {
                foreach ($langFiles as $fn) {
                    include($_lang . "/" . $fn);
                }
            } else {
                die ("Requested language '$lang' is not valid.");
            }
        } else {
            die ("Requested language '$lang' is not valid.");
        }
    } else {
        die ("Requested language '$lang' not found.");
    }
    
    
    
    function lang($lang_content_string) {
        global $_LANG;
        return (isset($_LANG[$lang_content_string])) ? $_LANG[$lang_content_string] : '[' . $lang_content_string . ']';
    }
    
    function lang_echo($lang_content_string) {
        echo lang($lang_content_string);
    }
    
    
    function html_escape_regional_chars($str) {
        $old = array('/Ä/','/Ö/','/Ü/','/ä/','/ö/','/ü/','/ß/');
        $new = array('&Auml;','&Ouml;','&Uuml;','&auml;','&ouml;','&uuml;','&szlig;');
        return preg_replace($old, $new, htmlspecialchars($str));
    }

?>