<?php
/**
 * @version     2010-06-15
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
 * @module      Includes necessary files for a web-call
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
 
//Note: this has to be changed if this file is moved somewhere else
$FBP = dirname(dirname(__FILE__)) . "/";
$FBP2 = $FULL_BASEPATH = dirname(dirname(__FILE__)); //FULL_BASEPATH is legacy

require("$FBP2/config/config.php");
require("$FBP2/config/version.php");

require("$FBP2/includes/database.php");
$db = new MySQLConnection($dbhost, $dbuser, $dbpass, $dbname); //create the new db object and give it the connection data
$db->connect(); //connect to database

//load additional options into variables for convenience
$lang = $defaultlang = $db->getOption("default_lang", "en");

require("$FBP2/lang/lang.php");
if (isset($_SESSION["lang"]))
    $lang = $_SESSION["lang"];
if (isset($_POST["newlang"]))
    $_SESSION["lang"] = $lang = $_POST["newlang"];
Lang::createLangList("$FBP2/lang", true);
Lang::readLangFilesFromDir("$FBP2/lang/$defaultlang", true);
if ($lang != $defaultlang)
    Lang::readLangFilesFromDir("$FBP2/lang/$lang");

require("$FBP2/includes/logging/Logger.php");
$log = new Logger;
$log->addLog("live", LEL_NOTICE, true, false, false, false);
$log->addLog("error", LEL_ERROR, false, true, false, false);
$log->addLog("debug", LEL_DEBUG, false, true, false, true);

require("$FBP2/includes/usrman/UsrMan.php");
UsrMan::readDB();

require("$FBP2/includes/pluginman/PluginMan.php");
ComMan::readDB();

?>