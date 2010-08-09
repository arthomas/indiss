<?php
/**
 * @version     2010-07-23
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

define("__LANG", 1);


/**
 * This class handles translation of strings.
 * Note: The class is implemented in such a way that if the active language is the
 * same as the default language, you need only load the default language.
 * @author Patrick Lehner
 */
class Lang {
    
    /**
     * Contains the list of available core languages.
     * @var array
     */
    private static $langList = array();
    /**
     * Contains all loaded messages for the current language.
     * @var array
     */
    private static $langMessages = array();
    /**
     * Contains all loaded messages for the default language.
     * @var array
     */
    private static $defaultLangMessages = array();
    
    /**
     * Create the list of available core languages.
     * @param string $dirname The directory in whose sub-folders to look for language info
     * files. In PHP-manner, without a trailing slash.
     * @param bool[optional] $defineAsGlobal If true, the language list is also stored in the global
     * variable $langList, otherwise only in the class property. If the global variable already
     * contains a value and this argument is true, that value will be overwritten. Defaults to false.
     */
    public static function createLangList($dirname, $defineAsGlobal = false) {
        if (!file_exists($dirname) || !is_dir($dirname)) {
            trigger_error(__METHOD__ . "(): Cannot create language list: This is not a directory ($dirname)", E_USER_ERROR);
            return;
        }
        $d = $dirname;
        $l = preg_grep("/\./i", scandir("$d"), PREG_GREP_INVERT); //scan for sub-folders (all that do not include a dot -- thus excluding "this dir" and "parent dir"
        if (empty($l)) {
            trigger_error(__METHOD__ . "(): Cannot create language list: No languages were found ($dirname)", E_USER_ERROR);
            return;
        }
        self::$langList = array();
        foreach ($l as $dir) {
            if (file_exists("$d/$dir/langinfo.xml")) {
                $x = simplexml_load_file("$d/$dir/langinfo.xml");
                if ($x !== false) {
                    //check that the three mandatory info values are defined and that the language code matches the directory name
                    if ((bool)$x->name && (bool)$x->code && (bool)$x->version && $dir == (string)$x->code) {
                        self::$langList[$dir] = (string)$x->name;
                    }
                } else {
                    //this means that loading the XML file failed. Since we cannot use our Logger class here yet, we will just debug with E_NOTICE level errors
                    trigger_error("Error while loading language info file ($d/$dir/langinfo.xml). Skipping.", E_USER_NOTICE);
                }
            }
        }
        //if requested so, we store the language list in the global var as well
        if ($defineAsGlobal)
            $GLOBALS["langList"] = self::$langList;  //note: right now, i'm not quite sure if this clones the array, or only stores another reference.
    }
    
    /**
     * Read a language file into memory.
     * If the file does not exist or if the passed path does not refer to a file, the function
     * will output an E_USER_ERROR level error. If the file is not a valid language file, the
     * function will output an E_USER_NOTICE level error and abort.
     * @param string $filename The path and name of the language file. It must either be an
     * XML file or a PHP file defining strings for a language.
     * @param bool[optional] $toDefaultLang If true, the strings will be stored to the default language
     * array, otherwise to the array for the current language. Defaults to false.
     */
    public static function readLangFile($filename, $toDefaultLang = false) {
        if (!file_exists($filename) || !is_file($filename)) {
            trigger_error(__METHOD__ . "(): Cannot load language file: This is not a file ($filename)", E_USER_ERROR);
            return;
        }
        $valid = true;
        $ext = strtolower(substr($filename, -3));
        switch ($ext) {
            case "xml":
                $x = simplexml_load_file($filename);
                if (($x === false) || $x->getName() != "language") {
                    $valid = false;
                    break;
                }
                $lang = array();
                foreach ($x as $item) {
                    $_LANG[(string)$item["name"]] = utf8_decode((string)$item["value"]);    //need utf8_decode() because we dont work with unicode otherwise
                }
                break;
            case "php":
                if (@include($filename) === false)
                    $valid = false;
                break;
            default:
                $valid = false;
        }
        if (!$valid) {
            trigger_error(__METHOD__ . "(): This is not a valid language file ($filename)", E_USER_NOTICE);
        }
        if ($toDefaultLang) {
            self::$defaultLangMessages = array_merge(self::$defaultLangMessages, $_LANG);
        } else {
            self::$langMessages = array_merge(self::$langMessages, $_LANG);
        }
    }
    
    /**
     * Read all language files within a directory into memory. This function will load
     * all files starting with "lang_" and ending in either ".xml" or ".php" (for the internal
     * layout of language files, please refer to the documentation).
     * If the passed path does not point to a directory, if it does not exist or if it does not
     * contain any language files, the function will output an E_USER_ERROR level error and abort.
     * @param string $dirname The directory from which to load the language files. Sub-directories
     * will not be searched. In PHP-manner, without a trailing slash.
     * @param bool[optional] $toDefaultLang If true, the strings will be stored to the default language
     * array, otherwise to the array for the current language. Defaults to false.
     */
    public static function readLangFilesFromDir($dirname, $toDefaultLang = false) {
        if (!file_exists($dirname) || !is_dir($dirname)) {
            trigger_error(__METHOD__ . "(): Cannot load language files: This is not a directory ($dirname)", E_USER_ERROR);
            return;
        }
        $langFiles = preg_grep("/lang_.+\.(?:xml|php)$/i", scandir("$dirname"));
        if (empty($langFiles)) {
            trigger_error(__METHOD__ . "(): No language files were found ($dirname)", E_USER_ERROR);
            return;
        }
        foreach ($langFiles as $fn) {
            self::readLangFile("$dirname/$fn", $toDefaultLang);
        }
    }
    
    /**
     * Escape certain regional characters to their HTML entities. The current implementation escapes
     * the following characters: ִײÜהצüß
     * @param string $str The string to be escaped.
     * @return string Returns the escaped string.
     */
    public static function html_escape_regional_chars($str) {
        $old = array('/ִ/','/ײ/','/Ü/','/ה/','/צ/','/ü/','/ß/');
        $new = array('&Auml;','&Ouml;','&Uuml;','&auml;','&ouml;','&uuml;','&szlig;');
        return preg_replace($old, $new, $str);
    }
    
    /**
     * Translate a string, if its translation is available.
     * @param (array OR string) $content If this is a string, it will be used as the key for the
     * translation string which will be returned. If it is an array, the first element in the array
     * will be the key and all other elements will be passed as arguments to vsprintf() to format
     * the retrieved string. Regional character escaping will take place after formatting the string.
     * @param bool[optional] $forJS Set this to true if you want to use the returned string with
     * Javascript. In that case, the regional characters will not be escaped.
     * @param bool[optional] $fromDefaultLang If this is true, the function will not check the current
     * language but instead return the default language version for the given key (if available).
     * @return string This function will return: the translated
     * version for the key given, if available; the default language version for the key given, if
     * available; the key given enclosed by braces ([]), if neither of the former is available.
     */
    public static function translate($content, $forJS = false, $fromDefaultLang = false) {
        //$content = array_values($content);  //this would support associative arrays -- left out for now to increase performance
        if (is_array($content)) {
            $str = $content[0];
            $args = array_slice($content, 1);
        } else {
            $str = $content;
            $args = array();
        }
        
        if (!$fromDefaultLang && isset(self::$langMessages[$str]))
            $s = self::$langMessages[$str];
        else
            $s = (isset(self::$defaultLangMessages[$str])) ? self::$defaultLangMessages[$str] : '[' . $str . ']';

        $s = vsprintf($s, $args);
        
        if (!$forJS)
            $s = self::html_escape_regional_chars($s);
        return $s;
    }
}

/**
 * A legacy wrapper for Lang::translate()
 * @deprecated This function is deprecated and only provided for compatibility. Use translate()
 * or Lang::translate() instead.
 * @param string $lang_content_string The key for the string.
 * @param bool[optional] $forJS True if the string will be used with Javascript (will not
 * replace regional characters). Defaults to false.
 * @return The translated string (see Lang::translate() for more details)
 */
function lang($lang_content_string, $forJS = false) {
    return Lang::translate($lang_content_string, $forJS);
}

/**
 * A legacy wrapper for Lang::translate(). echo's the retrieved string.
 * @deprecated This function is deprecated and only provided for compatibility. Use echo translate()
 * or echo Lang::translate() instead.
 * @param string $lang_content_string The key for the string.
 * @param bool[optional] $forJS True if the string will be used with Javascript (will not
 * replace regional characters). Defaults to false.
 */
function lang_echo($lang_content_string, $forJS = false) {
    echo Lang::translate($lang_content_string, $forJS);
}


/**
 * Translate a string, if its translation is available. Wrapper for Lang::translate().
 * @param (array OR string) $content If this is a string, it will be used as the key for the
 * translation string which will be returned. If it is an array, the first element in the array
 * will be the key and all other elements will be passed as arguments to vsprintf() to format
 * the retrieved string. Regional character escaping will take place after formatting the string.
 * @param bool[optional] $forJS Set this to true if you want to use the returned string with
 * Javascript. In that case, the regional characters will not be escaped.
 * @param bool[optional] $fromDefaultLang If this is true, the function will not check the current
 * language but instead return the default language version for the given key (if available).
 * @return string This function will return: the translated
 * version for the key given, if available; the default language version for the key given, if
 * available; the key given enclosed by braces ([]), if neither of the former is available.
 */
function translate($content, $forJS = false, $fromDefaultLang = false) {
    return Lang::translate($content, $forJS, $fromDefaultLang);
}

?>