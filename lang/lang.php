<?php
/**
 * @version     2010-06-04
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

//create the languages list
$d = dirname(__FILE__);
$l = preg_grep("/\./i", scandir("$d"), PREG_GREP_INVERT);
foreach ($l as $dir) {
    if (file_exists("$d/$dir/langinfo.xml")) {
        $x = simplexml_load_file("$d/$dir/langinfo.xml");
        if ($x !== false) {
            if ((bool)$x->name && (bool)$x->code && (bool)$x->version && $dir == (string)$x->code) {
                $langList[$dir] = (string)$x->name;
            }
        }
    }
}

$_lang = $d . "/$lang"; //create the full path of the language directory
if (file_exists($_lang)) {
    if (is_dir($_lang)) {
        $langFiles = preg_grep("/lang_.+\.xml$/i", scandir("$_lang"));
        if (!empty($langFiles)) {
            foreach ($langFiles as $fn) {
                $x = simplexml_load_file("$_lang/$fn");
                if (($x === false) || $x->getName() != "language") {
                    trigger_error("This file is not a valid language file ($_lang/$fn)");
                    continue;
                }
                foreach ($x as $item) {
                    $_LANG[(string)$item["name"]] = utf8_decode((string)$item["value"]);    //need utf8_decode() because we dont work with unicode otherwise
                }
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

unset($d, $_lang, $langFiles, $fn, $x);
    
    
    
function lang($lang_content_string, $forJS = false) {
    global $_LANG;
    $s = (isset($_LANG[$lang_content_string])) ? $_LANG[$lang_content_string] : '[' . $lang_content_string . ']';
    //$s = htmlspecialchars($s);
    //$s = utf8_decode($s);       //note: we need this because those XML files are 
    if (!$forJS)
        $s = html_escape_regional_chars($s);
    return $s;
}

function lang_echo($lang_content_string, $forJS = false) {
    echo lang($lang_content_string, $forJS);
}


function html_escape_regional_chars($str) {
    $old = array('/Ä/','/Ö/','/Ü/','/ä/','/ö/','/ü/','/ß/');
    $new = array('&Auml;','&Ouml;','&Uuml;','&auml;','&ouml;','&uuml;','&szlig;');
    return preg_replace($old, $new, $str);
}

?>