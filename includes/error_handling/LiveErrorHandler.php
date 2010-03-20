<?php
/**
 * @version     2010-03-20
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
 * @module      Live error message handler
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
 
class LiveErrorHandler {
    
    //---- Class Constants --------------------------------------------------------------
    
    const EK_DEBUG   = 0;
    const EK_NOTICE  = 1;
    const EK_WARNING = 2;
    const EK_ERROR   = 3;
    const EK_SUCCESS = 4;
    

    //---- Static properties ------------------------------------------------------------
    
    public static $handlers = array();
    private static $defaultColors = array(
        "white",
        "white",
        "#FFDDFF",
        "#FFDDDD",
        "#DDFFDD"
        );
    private static $lastHandler;
    
    
    //---- Object properties ------------------------------------------------------------
    
    private $name = "";
    private $messages = array();
    private $colors = array();
    
    
    //---- Static methods ---------------------------------------------------------------
    
    private static function checkColorArray($array) {
        if (is_array($array))
            if (count($array) == 5) {
                foreach ($array as $element)
                    if (!is_string($element)) {
                        return false;
                    }
                return true;
            }
        return false;
    }
    
    public static function count() {
        return count(self::$handlers);
    }
    
    public static function getDefaultColors() {
        return self::$defaultColors;
    }
    
    public static function setDefaultColors($defaultColors) {
        if (!checkColorArray($defaultColors)) {
            trigger_error("LiveErrorHandler::setDefaultColors(): color array does not meet requirements", E_USER_WARNING);
            return false;
        }
        self::$defaultColors = $defaultColors;
        return true;
    }
    
    public static function getLastHandler() {
        return self::$lastHandler;
    }
    
    public static function add($name = "", $colors = null) {
        $handler = new LiveErrorHandler($name, $colors);
        self::$handlers[] = $handler;
        self::$lastHandler = $handler;
        return $handler;
    }
    
    public static function remove(LiveErrorHandler $handler) {
        if (is_null($handler)) {
            trigger_error("LiveErrorHandler::remove(): first argument cannot be NULL", E_USER_WARNING);
            return false;
        }
        foreach (self::$handlers as $key => $_handler)
            if ($_handler === $handler) {
                return self::removeByIndex($key);
            }
        trigger_error("LiveErrorHandler::remove(): handler not found", E_USER_WARNING);
        return false;
    }
    
    public static function removeByName($name) {
        if (!is_string($name)) {
            trigger_error("LiveErrorHandler::removeByName(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        foreach (self::$handlers as $key => $handler)
            if ($handler->name === $name) {
                return self::removeByIndex($key);
            }
        trigger_error("LiveErrorHandler::removeByName(): handler named '$name' not found", E_USER_WARNING);
        return false;
    }
    
    public static function removeByIndex($index) {
        if (!is_int($index)) {
            trigger_error("LiveErrorHandler::removeByIndex(): first argument must be of type int", E_USER_WARNING);
            return false;
        }
        if (!is_null(self::$handlers[$index])) {
            self::$handlers[$index]->__destruct();
            self::$handlers[$index] = null;
            return true;
        } else {
            trigger_error("LiveErrorHandler::removeByIndex(): handler reference is already null", E_USER_WARNING);
            return false;
        }
        
    }
    
    
    //---- Constructors & destructors ---------------------------------------------------
    
    private function __construct($name, $colors = null) {
        $this->name = $name;
        if (is_null($colors))
            $this->colors = self::$defaultColors;
        else
            $this->colors = $colors;
    }
    
    /*
    private function __destruct() {
        
    }*/
    
    
    //---- Object methods ---------------------------------------------------------------
    
    public function index() {
        //Note: There should be a more elegant way to find the index.
        //      If anyone finds it, tell me :)
        foreach (self::$handlers as $key => $handler)
            if ($handler === $this)
                return $key;
    }
    
    public function getColors() {
        return $this->colors;
    }
    
    public function setColors($colors) {
        if (!checkColorArray($colors)) {
            trigger_error("LiveErrorHandler::setColors(): color array does not meet requirements", E_USER_WARNING);
            return false;
        }
        $this->colors = $colors;
        return true;
    }
    
    public function addMsg($origin, $message, $kind = self::EK_NOTICE) {
        if (!is_string($origin)) {
            trigger_error("LiveErrorHandler::addMsg(): first argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!is_string($message)) {
            trigger_error("LiveErrorHandler::addMsg(): second argument must be of type string", E_USER_WARNING);
            return false;
        }
        if (!is_int($kind)) {
            trigger_error("LiveErrorHandler::addMsg(): third argument must be of type int", E_USER_WARNING);
            return false;
        }
        $msg["origin"]  = $origin;
        $msg["message"] = $message;
        $msg["kind"]    = $kind;
        $this->messages[] = $msg;
        return true; 
    }
    
    public function getMsgCount() {
        return count($this->messages);
    }
    
    public function getMsg($index) {
        if (!is_int($index)) {
            trigger_error("LiveErrorHandler::getMsg(): first argument must be of type int", E_USER_WARNING);
            return false;
        }
        return $this->messages[$index];
    }
    
    public function getMessages() {
        return $this->messages;
    }
    
    public function getFormatted($template = "") {
        if (!(is_string($template) || is_null($template))) {
            trigger_error("LiveErrorHandler::getMsg(): first argument must be NULL or of type string", E_USER_WARNING);
            return false;
        }
        $str  = "<div class=\"messagebox\" id=\"LiveErrorHandler_" . ((empty($this->name)) ? $this->index() : $this->name) . "\">\n";
        $str .= "<table summary=\"\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"messagetable\"><tbody>\n";
        if (count($this->messages) == 0) {
            $str .= "<tr><td class=\"noMessages\">(No messages)</td></tr>\n";
        } else {
            foreach ($this->messages as $msg) {
                $str .= "<tr style=\"background-color: " . $this->colors[$msg["kind"]] . ";\">";
                $str .= "<td class=\"origin" . ((empty($msg["origin"])) ? " noOrigin" : "" ) . "\">" . $msg["origin"] . ((empty($msg["origin"])) ? "" : ":" ) . "</td>";
                $str .= "<td class=\"message\">" . $msg["message"] . "</td>";
                $str .= "</tr>\n";
            }
        }
        $str .= "</tbody></table>\n";
        $str .= "</div>\n";
        return $str;
    }
    
    public function clear() {
        $this->messages = array();
    }

}

?>