<?php
/**
 * @version     2009-11-25
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Installation script, page 3: Database setup
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />

    <link rel="stylesheet" type="text/css" href="installation.css" />
    
    <title><?php lang_echo("3PageTitle"); ?></title>
    
    <script type="text/javascript" language="Javascript">
    function checkDBdata() {
        var r = true;
        if (document.getElementById('dbhost').value == "") {
            document.getElementById('dbhostrow').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbhostnote').firstChild.nodeValue = "<?php lang_echo("3JSFieldEmpty"); ?>";
            r = false;
        } else {
        	document.getElementById('dbhostrow').style.backgroundColor = "transparent";
            document.getElementById('dbhostnote').firstChild.nodeValue = "";
        }
        if (document.getElementById('dbname').value == "") {
            document.getElementById('dbnamerow').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbnamenote').firstChild.nodeValue = "<?php lang_echo("3JSFieldEmpty"); ?>";
            r = false;
        } else {
            document.getElementById('dbnamerow').style.backgroundColor = "transparent";
            document.getElementById('dbnamenote').firstChild.nodeValue = "";
        }
        if (document.getElementById('dbuser').value == "") {
            document.getElementById('dbuserrow').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbusernote').firstChild.nodeValue = "<?php lang_echo("3JSFieldEmpty"); ?>";
            r = false;
        } else {
            document.getElementById('dbuserrow').style.backgroundColor = "transparent";
            document.getElementById('dbusernote').firstChild.nodeValue = "";
        }
        if (document.getElementById('dbpass1').value != document.getElementById('dbpass2').value) {
            document.getElementById('dbpass1row').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbpass2row').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbpassnote').firstChild.nodeValue = "<?php lang_echo("3JSPassNotMatch"); ?>";
            r = false;
        } else {
            document.getElementById('dbpass1row').style.backgroundColor = "transparent";
            document.getElementById('dbpass2row').style.backgroundColor = "transparent";
            document.getElementById('dbpassnote').firstChild.nodeValue = "";
        }

        return r;
    }
    </script>
</head>
<body>
    <fieldset id="container"><legend><?php lang_echo("3PageTitle"); ?></legend>
        <form method="post" action="?step=<?php echo ($step + 1); ?>">
            <p style="margin-top: 0;"><?php lang_echo("3PleaseEnterData"); ?></p>
            <p><?php lang_echo("3TablePermission"); ?></p>
            <p><?php lang_echo("3SecurityNote"); ?></p>
            <div class="enterdata">
                <table summary="" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td><?php lang_echo("3DBType"); ?>:</td>
                            <td>MySQL<div class="small"><?php lang_echo("3NoteOnlyMySQLSupported"); ?></div></td>
                        </tr>
                        <tr id="dbhostrow">
                            <td><?php lang_echo("3DBHost"); ?>:</td>
                            <td><input type="text" name="dbhost" id="dbhost" <?php if ( isset( $_POST["dbhost"] ) ) echo "value=\"" . $_POST["dbhost"] . "\" ";?>/> <span id="dbhostnote" style="color: red;" class="small">&nbsp;</span><div class="small"><?php lang_echo("3DBHostProbablyLocalhost"); ?></div></td>
                        </tr>
                        <tr id="dbnamerow">
                            <td><?php lang_echo("3DBName"); ?>:</td>
                            <td><input type="text" name="dbname" id="dbname" <?php if ( isset( $_POST["dbname"] ) ) echo "value=\"" . $_POST["dbname"] . "\" ";?>/> <span id="dbnamenote" style="color: red;" class="small">&nbsp;</span></td>
                        </tr>
                        <tr id="dbuserrow">
                            <td><?php lang_echo("3DBUser"); ?>:</td>
                            <td><input type="text" name="dbuser" id="dbuser" <?php if ( isset( $_POST["dbuser"] ) ) echo "value=\"" . $_POST["dbuser"] . "\" ";?>/> <span id="dbusernote" style="color: red;" class="small">&nbsp;</span></td>
                        </tr>
                        <tr id="dbpass1row">
                            <td style="padding-bottom: 0;"><?php lang_echo("3DBPass"); ?>:</td>
                            <td style="padding-bottom: 0;"><input type="password" name="dbpass1" id="dbpass1" <?php if ( isset( $_POST["dbpass1"] ) ) echo "value=\"" . $_POST["dbpass1"] . "\" ";?>/> <span id="dbpassnote" style="color: red;" class="small">&nbsp;</span></td>
                        </tr>
                        <tr id="dbpass2row">
                            <td style="padding-top: 2px;"><?php lang_echo("3DBPassConfirm"); ?>:</td>
                            <td style="padding-top: 2px;"><input type="password" name="dbpass2" id="dbpass2" <?php if ( isset( $_POST["dbpass2"] ) ) echo "value=\"" . $_POST["dbpass2"] . "\" ";?>/></td>
                        </tr>
                    </tbody>
                </table>
                <div id="dbiframecontainer" style="padding-top: 20px;">
                    <?php lang_echo("3DBConTestLater"); ?>
                </div>
            </div>
            <div>
                <table id="buttonbar" summary="" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td id="leftbox"><input type="button" name="back" value="< <?php lang_echo("genBack"); ?>" id="backbutton" onclick="this.form.action='?step=<?php echo ($step - 1); ?>'; this.form.submit();" /></td>
                            <td id="rightbox">
                                <input type="button" name="next" value="<?php lang_echo("genNext"); ?> >" id="nextbutton" onclick="if (checkDBdata()) this.form.submit();" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
<?php foreach ($_POST as $key => $value)
        if ( !in_array($key, array("next", "back", "dbhost", "dbname", "dbuser", "dbpass1", "dbpass2")) ) { ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
<?php }?>
        </form>
    </fieldset>
</body>
</html>