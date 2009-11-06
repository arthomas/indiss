<?php
/**
 * @version     2009-11-06
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
    
    <title>InfoScreen Installation - Step 1: Language selection</title>
</head>
<body>
    <fieldset id="container"><legend>InfoScreen Installation - Step 1: Language selection</legend>
        <form method="post" action="">
            <p style="margin-top: 0;">Welcome to the InfoScreen installation script!</p>
            <p>Please select the language for this installation:</p>
            <div>
                <select id="langlist" name="lang" size="20" style="width: 100%;">
<?php include ( "lang/languages.php" );
    foreach ( $languages as $langkey => $language ) {
        if ( $langkey == $defaultlang )
            $selected = " selected=\"selected\"";
        else
            $selected = ""; ?>
                    <option value="<?php echo $langkey;?>"<?php echo $selected; ?>><?php echo $language; ?></option>
<?php } ?>
                </select>
                <input type="checkbox" name="useasdefaultlang" value="yes" checked="checked" /> Use this language as default language for InfoScreen as well
            </div>
            <div>
                <table id="buttonbar" summary="" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td id="leftbox"></td>
                            <td id="rightbox"><input type="submit" name="next" value="Next >" id="nextbutton" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
<?php foreach ($_POST as $key => $value)
        if ( !in_array($key, array("lang", "useasdefaultlang", "next", "previous")) ) { ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
<?php }?>
        </form>
    </fieldset>
</body>
</html>