<?php
/**
 * @version     2010-08-08
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      Backend main page
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
    
$__startTime = microtime(true);

define("__MAIN", 1);

session_name("INDISSAdmin");
session_start();

//initialize it all
require_once("../includes/loaders/loader_admin.php");
    
    if (isset($_POST['submit'])) {
        switch ($_POST["task"]) {
            case "login":
                if (empty($_POST['username']) || empty($_POST['pw'])) {
                    if (empty($_POST['username'])) {
                        $usernamemissing = true;
                    }
                    if (empty($_POST['pw'])) {
                        $passwordmissing = true;
                    }
                } else {
                    UsrMan::login($_POST["username"], $_POST["pw"]);  // no need to remember the return value because "logged in" state is determined via $_SESSION
                }
                break;
            case "logout":          //log out: destroy session and all session data
                UsrMan::logout();
                break;
            case "register":
                /*$username = $_POST['username'];  //this whole sections needs to be changed and upgraded
                $pw = $_POST['pw'];
                $pw2 = $_POST['pw2'];
                $email = $_POST['email'];
                
                if ($pw != $pw2) {
                    mysql_close();
                    die("Password do not match.");
                }
                
                $pw = sha1($pw);
                
                $query = "INSERT INTO `users` (username, password, email) 
                            VALUES ('$username', '$pw', '$email')";
                
                $result = mysql_query($query);
                
                if (!$result) {
                    mysql_close();
                    die("The database query failed.");
                } else {
                    echo "Registration for user $username successful.";
                    mysql_close();
                    exit;
                }*/
                break;
            default:
                die(lang("errGeneralParamError"));
                break;
        }
    }
    
    if (!empty($_SESSION["uid"])) {
    	$activeUsr = User::getUser((int)$_SESSION["uid"]);
    }
    
    $task = $_GET["task"];
    
    if ($activeUsr) {
        $activePlugin = false;
        if (isset($_GET["pluginID"])) {
            if (($activePlugin = PluginMan::getPlugin((int)$_GET["pluginID"], true)) === false)  {
                $log->log("Global", LEL_ERROR, "A plugin with the ID " . $_GET["pluginID"] . " does not exist.");
            }
        }
        if (!$activePlugin && isset($_GET["plugin"])) {
        if (($activePlugin = PluginMan::getPluginByIname($_GET["plugin"], true)) === false)  {
                $log->log("Global", LEL_ERROR, "A plugin called '" . $_GET["plugin"] . "' does not exist.");
            }
        } 
        if (!$activePlugin) {
            $activePlugin = PluginMan::getPluginByIname("Overview");
        }
    } else {
        $activePlugin = PluginMan::getPluginByIname("LoginLogout");
        $task = "login";
    }
    
    if (!empty($_POST)) {
        $activePlugin->processInput($_POST["postview"]);
    }
    

    ob_start();
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />

    <link rel="stylesheet" type="text/css" href="css/admin_main.css" />
    <!--%CSSJSLINKS%-->
    
    <title><?php echo $sitename; ?> Administration</title>
</head>
<body>
    <div id="topBar">
        <div id="topBarInner">
<?php if ($activeUsr) { ?>
            <div id="topBarLogout" class="topBarRight">
                <form name="logoutform" id="logoutform" method="post" action="index.php">
                    <input type="hidden" name="task" value="logout" />
                    <input class="likeLink" type="submit" name="submit" value="<?php lang_echo("genLogout"); ?>" />
                </form>
            </div>
<?php } ?> 
        	<div id="topBarVer" class="topBarRight">Version: <?php echo __version(); ?></div>
            <div id="topBarTime" class="topBarRight">Seite erzeugt: <?php echo date("d.m.Y H:i:s", $_SERVER["REQUEST_TIME"]); ?></div>
            <div id="topBarLang" class="topBarRight">
                <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
                    <?php lang_echo("genLanguage");?>:
                    <select name="newlang" onchange="this.form.submit();">
<?php
    foreach ($langList as $key => $value) {
        $selected = ($key == $lang) ? " selected=\"selected\"" : "";
        echo "                        <option value=\"$key\"$selected>$value</option>\n";
    }
?>
                    </select>
                </form>
            </div>
            <div id="topBarSitename" class="topBarLeft"><?php echo $sitename; ?> Administration</div>
            <?php if ($activeUsr) { ?><div id="topBarUser" class="topBarLeft">Logged in as: <?php echo $activeUsr->getUname();?></div>
            <div id="topBarMenu" class="topBarLeft"><a href="index.php">Overview</a></div><?php } ?> 
            <div class="topBarContent">&nbsp;</div>
        </div>
    </div>
    <div id="main">
        <div id="output">
            <?php //output plugin nav here ?>
            <!--%HANDLEROUTPUT_COMMON%-->
            <div id="<?php $activePlugin->getIname(); ?>" class="<?php get_class($activePlugin); ?>">
                <?php $activePlugin->outputAdmin($task); ?> 
            </div>
        </div>
    </div>
    <div id="footer">
        <?php lang_echo("admLayoutAndRealization");?> &copy; 2009-2010 Patrick Lehner &nbsp; | &nbsp; <?php lang_echo("admIndissIsFreeSoftware");?><br />
        <?php echo sprintf(lang("admPageCreatedIn"), sprintf("%6.4f",(microtime(true) - $__startTime))); ?>
    </div>
</body>
</html>
<?php

$buf = ob_get_clean();  //retrieve all created (buffered) output

//Inject the LiveErrorHandler message output
/* 
 * Note: This part gives components the chance to place the message output differently (i.e. in a more fitting place); if
 * a component does not place the message output by inluding the string "<!--%HANDLEROUTPUT-->" in the appropriate place,
 * the message output will be placed in the default location above the component output. 
 * */
$count = 0;
$buf = str_replace("<!--%HANDLEROUTPUT%-->", ($handler->getMsgCount() > 0) ? $handler->getFormatted() : "", $buf, $count);
if ($count == 0)
    $buf = str_replace("<!--%HANDLEROUTPUT_COMMON%-->", ($handler->getMsgCount() > 0) ? $handler->getFormatted() : "", $buf);
unset ($count);

echo $buf;

mysql_close(); 
?>