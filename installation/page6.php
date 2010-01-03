<?php
/**
 * @version     2010-01-03
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      Installation script, page 6: Installation and install report
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
    
    $fatal = false;  //this will remember if there was a fatal error
    
    unset( $errors );
    unset( $log );
    
    include_once("db_functions.php");
    
    // Open connection to MySQL server
    if(!mysql_connect($_POST["dbhost"], $_POST["dbuser"], $_POST["dbpass1"])) { 
        $errors[] = $log[] = lang("6ErrMySQLConnFailed") . mysql_error();
        $fatal = true;
    } else {
        $log[] = lang("6LogMySQLConnSuccess");
        $dbconnected = true;
    }
    
    if ( empty( $errors ) ) {
        $configFile = file_get_contents("config.php-dist");
        if (!$configFile) {
            $errors[] = $log[] = "Opening template for config file failed.";
            $fatal = true;
        } else {
            $log[] = "Opening template for config file successful.";
        }
    }
    
    if ( empty( $errors ) ) {
        $db_list = mysql_list_dbs();
        $dbname = $_POST["dbname"];
        $dbfound = false;
        for ( $i = 0; $i < mysql_num_rows($db_list); $i++ ) {
            if ( mysql_db_name($db_list, $i) == $dbname ) {
                $dbfound = true;
                break;
            }
        }
        
        if ( $dbfound ) {
            $log[] = "Database '$dbname' already exists.";
        } else {
            $log[] = "Database '$dbname' does not exist.";
            
            $result = mysql_query( "CREATE DATABASE `$dbname`" );
            if ( !$result ) {
                $errors[] = $log[] = "Error: Creating database '$dbname' failed; MySQL said: " . mysql_error;
                $fatal = true;
                //note: should we insert an option to continue even if creating fails?
            } else {
                $log[] = "Database '$dbname' successfully created.";
            }
        }
        
        if ( empty( $errors ) ) {
            // Open the right database
            if(!mysql_select_db($dbname)) { 
                mysql_close();
                $errors[] = $log[] = lang("6ErrMySQLDBSelFailed") . mysql_error();
                $fatal = true;
            } else {
                $log[] = "Database '$dbname' successfully selected.";
            }
            
            if ( empty( $errors ) ) {  //if there was no error during opening DB connection in database.php
    
                /*Create Table for user login data*/
                $query = 
                    "CREATE TABLE `users` (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `username` VARCHAR( 255 ) NOT NULL ,
                    `password` VARCHAR( 255 ) NOT NULL ,
                    `email` VARCHAR( 255 ) NOT NULL ,
                    `type` ENUM( 'admin', 'user' ) NOT NULL
                    )";
                $msg = "Create table 'users' for user login data... ";
                if ( db_commit2( $query, $errors ) )
                    $msg .= "Success!";
                else
                    $msg .= "Error!";
                $log[] = $msg;
                
                /*Create Table for ticker data*/
                $query = 
                    "CREATE TABLE `com_tickers` (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `caption` VARCHAR( 255 ) NOT NULL ,
                    `content` VARCHAR( 255 ) NOT NULL ,
                    `start` DATETIME NOT NULL ,
                    `end` DATETIME NOT NULL,
                    `enabled` BOOL NOT NULL,
                    `deleted` BOOL NOT NULL
                    )";
                $msg = "Create table 'users' for user login data... ";
                if ( db_commit2( $query, $errors ) )
                    $msg .= "Success!";
                else
                    $msg .= "Error!";
                $log[] = $msg;
                
                /*Create Table for content data*/
                $query = 
                    "CREATE TABLE `com_content` (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `name` VARCHAR( 255 ) NOT NULL ,
                    `url` VARCHAR( 255 ) NOT NULL ,
                    `displaytime` INT NOT NULL,
                    `start` DATETIME NOT NULL ,
                    `end` DATETIME NOT NULL,
                    `enabled` BOOL NOT NULL,
                    `deleted` BOOL NOT NULL
                    )";
                $msg = "Create table 'com_content' for content data... ";
                if ( db_commit2( $query, $errors ) )
                    $msg .= "Success!";
                else
                    $msg .= "Error!";
                $log[] = $msg;
            
                /*Create Table for error log*/
                $query = 
                    "CREATE TABLE `errors` (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `date` DATETIME NOT NULL ,
                    `content` VARCHAR( 255 ) NOT NULL ,
                    `new` BOOL NOT NULL
                    )";
                $msg = "Create table 'errors' for error log... ";
                if ( db_commit2( $query, $errors ) )
                    $msg .= "Success!";
                else
                    $msg .= "Error!";
                $log[] = $msg;
            
                $log[] = "Creating standard name-value tables and saving default values:";
                
                
                include_once ("defaultvalues.php");
                /*Create standard name-value tables and create default values*/
                foreach ($DV as $key => $values) {
                    $query = makeNameValueTableQuery($key);
                    $msg = "Create name-value table '$key'... ";
                    if ( db_commit2( $query, $errors ) )
                        $msg .= "Success!";
                    else
                        $msg .= "Error!";
                    $log[] = $msg;
                    if ( !empty( $values ) ) {
                        foreach ($values as $value) {
                            $query =
                                "INSERT INTO `$key` (`name`, `value`, `comment`)" .
                                "VALUES (" .
                                    "'" . $value['name'] . "'," .
                                    "'" . $_POST["dvt;".$key.";".$value["name"]] . "'," .
                                    "'" . $value['comment'] . "'" .
                                ")";
                            $msg = "Saving setting '" . $value["name"] . "' with value '" . $_POST["dvt;".$key.";".$value["name"]] . "' to table '$key' ... ";
                            if ( db_commit2( $query, $errors ) )
                                $msg .= "Success!";
                            else
                                $msg .= "Error!";
                            $log[] = $msg;
                        }
                    }
                }
                
                $configFile = str_replace("%defaultlang%", $lang, $configFile);
                $configFile = str_replace("%dbhost%", $_POST["dbhost"], $configFile);
                $configFile = str_replace("%dbuser%", $_POST["dbuser"], $configFile);
                $configFile = str_replace("%dbpass%", $_POST["dbpass1"], $configFile);
                $configFile = str_replace("%dbname%", $_POST["dbname"], $configFile);
                
                if (file_put_contents("../config/config.php", $configFile)) {
                    $log[] = "Config file written to disk successfully.";
                } else {
                    $errors[] = $log[] = "Writing config file to disk failed.";
                }
            
            } else {
                $errors[] = $log[] = "Connection to database not successful. Aborting...";
            }
            
        }
    }
    
    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />

    <link rel="stylesheet" type="text/css" href="installation.css" />
    
    <script type="text/javascript">
    function toggleVis( id, linkid, text ) {
        o=document.getElementById(id);
        if (o.style.display != "none") {
            o.style.display = "none";
            document.getElementById(linkid).firstChild.nodeValue = "[Show " + text + "]";
        }
        else {
            o.style.display = "block";
            document.getElementById(linkid).firstChild.nodeValue = "[Hide " + text + "]";
        }
    }
    </script>
    
    <title><?php lang_echo("6PageTitle"); ?></title>
</head>
<body>
    <fieldset id="container"><legend><?php lang_echo("6PageTitle"); ?></legend>
        <form method="post" action="?step=<?php echo ($step + 1); ?>">
            <p style="margin-top: 0;">
                <?php
                if ( empty( $errors ) && !$fatal ) { 
                    lang_echo("6Success");
                } else {
                    if ( $fatal ) {
                        echo "Es trat ein fataler Fehler auf. Die Installation wurde abgebrochen.<br />\n";
                    }
                    echo "Es traten " . ( (empty($errors)) ? "keine" : count( $errors ) ) . " Fehler auf.<br />\n";
                } 
                echo "Es wurden " . ( (empty($warnings)) ? "keine" : count( $warnings ) ) . " Warnungen ausgegeben." 
                ?> 
            </p>
            <div class="enterdata">
<?php if (!empty($errors)) { ?>
                <div style="float:right;font-size:80%;"><a id="errorstoggle" href="javascript:toggleVis('errors', 'errorstoggle', 'error list');">Show error list</a></div><h3>Errors</h3>
                <ul class="level1" id="errors" style="display:none;">
<?php foreach ($errors as $entry) { ?>
                    <li><?php echo $entry; ?></li>
<?php } ?>
                </ul>
<?php } else { ?>
                <h3>No errors occurred!</h3>
<?php }?>
            </div>
            <div class="enterdata">
                <div style="float:right;font-size:80%;"><a id="detailstoggle" href="javascript:toggleVis('details', 'detailstoggle', 'installation log');">Show installation log</a></div><h3>Detailed installation log</h3>
                <ul class="level1" id="details" style="display:none;">
<?php foreach ($log as $entry) { ?>
                    <li><?php echo $entry; ?></li>
<?php } ?>
                </ul>
            </div>
            <div>
                <table id="buttonbar" summary="" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td id="leftbox"><input type="button" name="back" value="< <?php lang_echo("genBack"); ?>" id="backbutton" onclick="this.form.action='?step=<?php echo ($step - 1); ?>'; this.form.submit();" /></td>
                            <td id="rightbox"><input type="submit" name="next" value="<?php lang_echo("genInstall"); ?> >" id="nextbutton" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
<?php /* foreach ($_POST as $key => $value)
        if ( !in_array($key, array("next", "back")) ) { ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
<?php } */ ?>
        </form>
    </fieldset>
</body>
</html>