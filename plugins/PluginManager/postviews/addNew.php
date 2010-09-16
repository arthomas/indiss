<?php
/**
 * @version     2010-09-16
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

//note that this file's scope is within a function! it is being included from within PluginPluginManager::processInput()
 
defined("__MAIN") or die("Restricted access.");
class_exists("PluginPluginManager") or die("Class 'PluginPluginManager' is unknown [" . __FILE__ . "]");

global $FBP;

if ($_POST["sourcetype"] == "upload") {
    if (is_uploaded_file($_FILES["pluginFile"]["tmp_name"])) {
        $zipfile = $_FILES["pluginFile"]["tmp_name"];
        $destfolder = $FBP . "temp/" . basename($_FILES["pluginFile"]["tmp_name"]);
    } else {
        //Attention: need to observe behaviour of this statement if there is code after the inclusion call in PluginPluginManager!
        return;
    }
} else if ($_POST["sourcetype"] == "download") {
    
}

//open ZIP file
$z = new ZipArchive();
$z->open($zipfile);

/*//output debug info
print_r($z);
var_dump($z);
echo "numFiles: " . $z->numFiles . "\n";
echo "status: " . $z->status  . "\n";
echo "statusSys: " . $z->statusSys . "\n";
echo "filename: " . $z->filename . "\n";
echo "comment: " . $z->comment . "\n";

for ($i=0; $i<$z->numFiles;$i++) {
    echo "index: $i\n";
    print_r($z->statIndex($i));
}*/

//check if ZIP file is a valid plugin archive
$valid = ($z->numFiles > 0);
if ($valid) { //the first entry in the archive must be a folder
    $item = $z->statIndex(0);
    $valid = (substr($item["name"], -1) == "/") && ($item["size"] == 0);
    $foldername = substr($item["name"], 0, -1);
}
if ($valid) { //and within that top-level folder must be a pluginInfo.xml file
    $item = $z->statName($foldername . "/pluginInfo.xml");
    $valid = ($z !== false);
}

$error = false;

if ($valid) {
    $destfolder = str_replace("\\","/", $destfolder); //replace any backslashes from Windows OS with slashes, or extractTo() will act up
    if ($z->extractTo($destfolder)) {
        
    } else {
        //output msg that extraction failed
        echo "extract error";
        $error = true;
    }
} else { //not $valid
    //output msg that archive is not valid
    $error = true;
    echo "not valid";
}

//we are done with the ZIP file. close it to free its resources
$z->close();

if (!$error) {
    //the rest of the installation is now done by PluginMan:
    if (PluginMan::installKind($destfolder . "/" . $foldername))
        $log->log("Plugin manager", LEL_NOTICE, "Successfully installed plugin");
}

include_once($FBP . "includes/filesystem/recursiveDelete.php");
echo "recursive delete folder";
recursiveDelete($path . "/" . $foldername);

?>