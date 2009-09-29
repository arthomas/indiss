<?php
/**
 * @version     2009-09-28
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
	define("__MAIN", 1);

	include_once("../config/config.php");
	include_once("../includes/database.php");
	//include_once("../config/version.php");  //TODO: admin: Version management
	
	static $lang;
	if (!isset($lang))
        $lang = $defaultlang;
    if (isset($_POST["newlang"]))
        $lang = $_POST["newlang"];
	include("lang/lang.php");
	
	session_name("InfoScreenAdmin");
    session_start();
    
    if (isset($_POST['submit'])) {
        if ($_POST['task'] == 'login') {
            if (!empty($_POST['username']) && !empty($_POST['pw'])) {
                $username = $_POST['username'];
	            $pw = sha1($_POST['pw']);
	            $ip = $_SERVER['REMOTE_ADDR'];
	            
                $query = "SELECT username,password 
	                        FROM   `users`
	                        WHERE  username='$username'";
	            
                $result = mysql_query($query);
                if (!$result) { 
                    mysql_close();
                    die(lang("errDBQryFailed"));
                }
                
                $row = mysql_fetch_object($result);
                if (($row->password != $pw) || (mysql_num_rows($result) == 0)) {
                    $loginresult = false;
                    $message .= lang("msgWrongPWorUN") . "<br />\n"; /*"Wrong password or username"*/
                    if (mysql_num_rows($result) == 0) {
                        $query = "INSERT INTO `errors` (`date`, `content`, `new`)
                                    VALUES (
                                        '". date($datefmt) . "',
                                        'Someone tried to login with unknown username \"$username\" from IP $ip',
                                        TRUE
                                    )";
                        $result = mysql_query($query);
                        if (!$result) { 
                            mysql_close();
                            die(lang("errDBQryFailed"));
                        }
                    }
                    else if ($row->password != $pw) {
                        $query = "INSERT INTO `errors` (`date`, `content`, `new`)
                                    VALUES (
                                        '". date($datefmt) . "',
                                        'Someone tried to log in as \"$username\" with wrong password from IP $ip'
                                    )";
                        $result = mysql_query($query);
                        if (!$result) { 
                            mysql_close();
                            die(lang("errDBQryFailed"));
                        }
                    }
                } else {
                    $_SESSION['username'] = $username;
                    $_SESSION['sid'] = session_id(); 
                    $_SESSION['ip'] = $ip;
                    $loginresult = true;
                    $message .= lang("msgLoginSuccess") . "<br />\n";
                    
                    if ($errview = getValueByName("global_options", "display_new_errors")) {
                        if ($errview == "admin_notify") {
                            $query = "SELECT COUNT()
                                        FROM `errors`
                                        WHERE `new`=TRUE";
                            $result = mysql_query($query);
                            if (!$result) { 
                                mysql_close();
                                die(lang("errDBQryFailed"));
                            }
                            $new = mysql_fetch_row($result);
                            if ($new > 0)
                                $message .= "There are $new new entries in the error log.<br />\n";
                        } else if ($erroview == "admin_list") {
                            $query = "SELECT `date`,`content`
                                        FROM `errors`
                                        WHERE `new`=TRUE";
                            $result = mysql_query($query);
                            if (!$result) { 
                                mysql_close();
                                die(lang("errDBQryFailed"));
                            }
                            while ($new = mysql_fetch_object($result))
                                $message .= $new->date . ": " . $new->content . "<br />\n";
                        }
                    } else { 
                        mysql_close();
                        die(lang("errDBQryFailed"));
                    }
                }

            } else {
                if (empty($_POST['username'])) {
                    $usernamemissing = true;
                }
                if (empty($_POST['pw'])) {
                    $passwordmissing = true;
                }
            }
    		
    	} else if ($_POST['task'] == 'register') {
    		$username = $_POST['username'];
		    $pw = $_POST['pw'];
		    $pw2 = $_POST['pw2'];
		    $email = $_POST['email'];
    		
		    if ($pw != $pw2) {
		    	mysql_close();
		    	die("Password do not match.");                               /*Needs to be changed*/
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
		    }
		    
    	} else {
    		die(lang("errGeneralParamError"));
    	}
    } else if (isset($_GET["logout"])) {  //log out: destroy session and all session data
    	if (isset($_SESSION['username'])) {
    	    
    		$_SESSION = array();

            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }

            session_destroy();
            
    		$message .= lang("msgLogoutSuccess") . "<br />\n";
    	} else {
    		$message .= lang("errCantLogout") . "<br />\n";
    	}
    }
    
    if (!isset($_SESSION['username'])) {
        $showLoginScreen = true;
    } else if (!empty($_SESSION['username'])) {
    	$loggedin = true;
    }
    
    include("components/_comlist.php");
    if ($loggedin) {
	    if (!isset($_GET["component"]))
		    $component = $_comlist["overview"];
		else {
		    $component = $_comlist[$_GET["component"]];
			if (!isset($_comlist[$_GET["component"]]) || !file_exists($component)) {
			    $message .= lang("errComNotFound") . "<br />\n";
			    $component = $_comlist["overview"];
			}
		}
    } else {
    	$component = $_comlist["login"];
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />

    <link rel="stylesheet" type="text/css" href="css/admin_main.css" />
<?php 
    if (file_exists("../components/com_" . $_GET["component"] . "/admin.css.php"))
        echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"../components/com_" . $_GET["component"] . "/admin.css.php\" />\n";
?>
    
    <title><?php echo $sitename; ?> Administration</title>
</head>
<body>
    <div id="topBar">
        <div id="topBarInner">
            <?php if ($loggedin) { ?><div id="topBarLogout" class="topBarRight"><a href="index.php?logout">[<?php lang_echo("genLogout"); ?>]</a></div><?php } ?> 
        	<div id="topBarVer" class="topBarRight">Version: <?php echo $version; ?></div>
            <div id="topBarTime" class="topBarRight">Seite erzeugt: <?php echo date("d.m.Y H:i:s", $_SERVER["REQUEST_TIME"]); ?></div>
            <div id="topBarLang" class="topBarRight">
                <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
                    <?php lang_echo("genLanguage");?>:
                    <select name="newlang" onchange="this.form.submit();">
<?php
    include_once("lang/languages.php");
    foreach ($languages as $key => $value) {
        $selected = ($key = $lang) ? " selected=\"selected\"" : "";
        echo "                        <option value=\"$key\"$selected>$value</option>\n";
    }
?>
                    </select>
                </form>
            </div>
            <div id="topBarSitename" class="topBarLeft"><?php echo $sitename; ?> Administration</div>
            <?php if ($loggedin) { ?><div id="topBarUser" class="topBarLeft">Logged in as: <?php echo $_SESSION['username'];?></div>
            <div id="topBarMenu" class="topBarLeft"><a href="?component=overview">Overview</a></div><?php } ?> 
            <div class="topBarContent">&nbsp;</div>
        </div>
    </div>
    <div id="main">
        <div id="component">
<?php if (isset($message)) { echo "            <div id=\"messageBar\">$message</div>\n"; unset($message); } ?>
            <?php include($component); ?> 
        </div>
    </div>
    <div id="footer">
        Layout und Umsetzung &copy; 2009 Patrick Lehner &nbsp; | &nbsp; InfoScreen ist freie Software unter der GNU GPLv3
    </div>
</body>
</html>
<?php mysql_close(); ?>