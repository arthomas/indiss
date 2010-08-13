<?php
/**
 * @version     2010-08-13
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
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
defined("__CONFIGFILE") or die("Config file not included [" . __FILE__ . "]");
 
class CSSJSHandler {

    //---- Static properties ------------------------------------------------------------
    
    private static
        $scripts = array(),
        $scriptUrls = array(),
        $styles = array(),
        $styleUrls = array();
    
    
    //---- Object properties ------------------------------------------------------------
    
    
    //---- Static methods ---------------------------------------------------------------
    
    public static function addStyle($style) {
        self::$styles[] = $style;
    }
    
    public static function addStyleUrl($url) {
        self::$styleUrls[] = $url;
    }
    
    public static function addScript($script) {
        self::$scripts[] = $script;
    }
    
    public static function addScriptUrl($url) {
        self::$scriptUrls = $url;
    }
    
    private static function outputStyles(&$indentStr) {
        $s = "";
        
        if (count(self::$styles) > 0) {
            foreach (self::$styles as $style) {
                $s .= $indentStr . "<style type=\"text/css\">\n";
                $s .= $style . "\n";
                $s .= $indentStr . "</style>\n";
            }
            $s .= "\n";
        }
        
        return $s;
    }
    
    private static function outputStyleUrls(&$indentStr) {
        $s = "";
        
        if (count(self::$styleUrls) > 0) {
            foreach (self::$styleUrls as $url) {
                $s .= $indentStr . "<link rel=\"stylesheet\" type=\"text/css\" href=\"$url\" />\n";
            }
            $s .= "\n";
        }
        
        return $s;
    }
    
    private static function outputScripts(&$indentStr) {
        $s = "";
        
        if (count(self::$scripts) > 0) {
            foreach (self::$scripts as $script) {
                $s .= $indentStr . "<script type=\"text/javascript\">\n";
                $s .= $script . "\n";
                $s .= $indentStr . "</script>\n";
            }
            $s .= "\n";
        }
        
        return $s;
    }
    
    private static function outputScriptUrls(&$indentStr) {
        $s = "";
        
        if (count(self::$scriptUrls) > 0) {
            foreach (self::$scriptUrls as $url) {
                $s .= $indentStr . "<link rel=\"stylesheet\" type=\"text/css\" href=\"$url\" />\n";
            }
            $s .= "\n";
        }
        
        return $s;
    }
    
    public static function outputAll($indent = 0) {
        $s = "";
        for ($i = 0; $i < $indent; $i++)
            $s .= " ";
        
        $r = "";
        $r .= self::outputStyleUrls($s);
        $r .= self::outputScriptUrls($s);
        $r .= self::outputStyles($s);
        $r .= self::outputScripts($s);
        
        return $r;
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    
    //---- Object methods ---------------------------------------------------------------
    

}

?>