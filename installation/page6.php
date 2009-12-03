<?php
/**
 * @version     2009-12-03
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
    
    
    
    $dbhost = $_POST["dbhost"];
    $dbname = $_POST["dbname"];
    $dbuser = $_POST["dbuser"];
    $dbpass = $_POST["dbpass1"];
    
    $configfile    = true;  //need this to cheat database.php
    
    include_once("../includes/database.php");
    
    unset( $errors );
    
    function makeNameValueTableQuery ( $tablename ) {
        return 
            "CREATE TABLE `$tablename` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `name` VARCHAR( 255 ) NOT NULL ,
            `value` VARCHAR( 255 ) NOT NULL ,
            `comment` VARCHAR( 255 ) NOT NULL
            )";
    }


    /*Create Table for user login data*/
    $query = 
        "CREATE TABLE `users` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `username` VARCHAR( 255 ) NOT NULL ,
        `password` VARCHAR( 255 ) NOT NULL ,
        `email` VARCHAR( 255 ) NOT NULL ,
        `type` ENUM( 'admin', 'user' ) NOT NULL
        )";
    db_commit2( $query, $errors );

    /*Create Table for global options*/
    $query = makeNameValueTableQuery("global_options");
    db_commit2( $query, $errors );
    $query = 
        "INSERT INTO `global_options`
        (`name`, `value`) 
        VALUES
        ('display_new_errors', 'admin')";
    db_commit2( $query, $errors );

    /*Create Table for global view options*/
    $query = makeNameValueTableQuery("global_view_options");
    db_commit2( $query, $errors );

    /*Create Table for default view options*/
    $query = makeNameValueTableQuery("view_default_view");
    db_commit2( $query, $errors );

    /*Create Table for ticker options*/
    $query = makeNameValueTableQuery("com_tickers_options");
    db_commit2( $query, $errors );
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
    db_commit2( $query, $errors );

    /*Create Table for content options*/
    $query = makeNameValueTableQuery("com_content_options");
    db_commit2( $query, $errors );
    $query =
        "INSERT INTO `com_content_options`
        (`name`, `value`)
        VALUES
        ('default_display_time', 120),
        ('error_display_time', 10),
        ('max_width', 'auto'),
        ('max_height', 'auto')";
    db_commit2( $query, $errors );
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
    db_commit2( $query, $errors );

    /*Create Table for error log*/
    $query = 
        "CREATE TABLE `errors` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `date` DATETIME NOT NULL ,
        `content` VARCHAR( 255 ) NOT NULL ,
        `new` BOOL NOT NULL
        )";
    db_commit2( $query, $errors );
    
    /*Create Table for substitution table options*/
    $query = makeNameValueTableQuery("com_substtable_options");
    db_commit2( $query, $errors );
    
    /*Create Table for headline options*/
    $query = makeNameValueTableQuery("com_headline_options");
    db_commit2( $query, $errors );

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
            <p style="margin-top: 0;"><?php lang_echo("6Success"); ?></p>
            <div class="enterdata">
<?php if (!empty($errors)) { ?>
                <div style="float:right;font-size:80%;"><a id="errorstoggle" href="javascript:toggleVis('errors', 'errorstoggle', 'error list');">Show error list</a></div><h3>Errors</h3>
                <ul class="level1" id="errors" style="display:none;">
                </ul>
<?php } else { ?>
                <h3>No errors occurred!</h3>
<?php }?>
            </div>
            <div class="enterdata">
                <div style="float:right;font-size:80%;"><a id="detailstoggle" href="javascript:toggleVis('details', 'detailstoggle', 'installation log');">Show installation log</a></div><h3>Detailed installation log</h3>
                <ul class="level1" id="details" style="display:none;">
                    <li>Test</li>
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