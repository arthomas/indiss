<?php
/**
 * @version     2009-11-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Installation script, page 2: Intro and License
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
    //TODO: install script, step 1: might wanna enable auto-select if there is only one language available

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />

    <link rel="stylesheet" type="text/css" href="installation.css" />
    
    <title><?php lang_echo("2PageTitle"); ?></title>
</head>
<body>
    <fieldset id="container"><legend><?php lang_echo("2PageTitle"); ?></legend>
        <form method="post" action="">
            <p style="margin-top: 0;">This wizard will guide you through the installation of InfoScreen. It will ask you
            to enter some default settings and an administrator password. Once you have completed entering all necessary
            data, the script will create the required database tables and save all settings.</p>
            <p>Please note: This script will change neither the database nor the file system until you confirm all settings
            in the final installation step.</p>
            <p>In the following, a full copy of the GNU General Public License v3 will be displayed. By installing and
            using InfoScreen, you agree to this license, even if you by some means skip this step or the whole installation
            script.</p>
            <div id="license"><pre><?php echo htmlspecialchars(file_get_contents("lang/$lang/license.html")); ?></pre> 
            </div>
            <div>
                <table id="buttonbar" summary="" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td id="leftbox"><input type="button" name="back" value="< Back" id="backbutton" onclick="this.form.action='?step=1'; this.form.submit();" /></td>
                            <td id="rightbox"><input type="submit" name="next" value="Next >" id="nextbutton" /></td>
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