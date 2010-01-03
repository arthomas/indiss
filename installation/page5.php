<?php
/**
 * @version     2010-01-03
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      Installation script, page 5: Pre-Installation summary
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
    
    <title><?php lang_echo("5PageTitle"); ?></title>
</head>
<body>
    <fieldset id="container"><legend><?php lang_echo("5PageTitle"); ?></legend>
        <form method="post" action="?step=<?php echo ($step + 1); ?>">
            <p style="margin-top: 0;"><?php lang_echo("5Summary"); ?></p>
            <div class="enterdata">
                <script type="text/javascript">
                var shown=false;
                function toggleDVT() {
                    if (shown) {
                        document.getElementById("dvt").style.display = "none";
                        document.getElementById("togglelink").firstChild.nodeValue = "[Show default values]";
                        shown = false;
                    }
                    else {
                        document.getElementById("dvt").style.display = "block";
                        document.getElementById("togglelink").firstChild.nodeValue = "[Hide default values]";
                        shown = true;
                    }
                }
                </script>
                <ul class="level1">
                    <li>Language
                        <ul class="level2">
                            <li>User interface default language: <?php include ( "lang/languages.php" ); echo $languages[$_POST["lang"]]; ?></li>
                        </ul>
                    </li>
                    <li>Database
                        <ul class="level2">
                            <li>Database type: <?php echo $_POST["dbtype"]; ?></li>
                            <li>Database host: <?php echo $_POST["dbhost"]; ?></li>
                            <li>Database name: <?php echo $_POST["dbname"]; ?></li>
                            <li>Database username: <?php echo $_POST["dbuser"]; ?></li>
                            <li>Database password: <span style="font-style: italic;">(entered)</span></li>
                        </ul>
                    </li>
                    <li><div style="float:right; font-size: 80%;"><a id="togglelink" href="javascript:toggleDVT();">[Show default values]</a></div>Default values
                        <ul class="level2" id="dvt" style="display: none;">
<?php include ("defaultvalues.php"); 
    foreach ($DV as $key => $values) { ?>
                            <li><?php lang_echo("DV_$key"); ?>
                                <ul class="level3">
<?php   foreach ($values as $value) { ?>
                                    <li><?php echo $value["name"] . ": " . $_POST["dvt;" . $key . ";" . $value["name"]]; ?></li>
<?php   } ?>
                                </ul>
                            </li>
<?php } ?>
                        </ul>
                    </li>
                </ul>
            </div>
            <p><?php lang_echo("5LastChance"); ?></p>
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
<?php foreach ($_POST as $key => $value)
        if ( !in_array($key, array("next", "back")) ) { ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
<?php }?>
        </form>
    </fieldset>
</body>
</html>