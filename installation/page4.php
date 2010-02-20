<?php
/**
 * @version     2010-02-20
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      Installation script, page 4: Setup and default settings
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
    
    <title><?php lang_echo("4PageTitle"); ?></title>
    
</head>
<body>
    <fieldset id="container"><legend><?php lang_echo("4PageTitle"); ?></legend>
        <form method="post" action="?step=<?php echo ($step + 1); ?>">
            <p style="margin-top: 0;"><?php lang_echo("4EnterSettings"); ?></p>
            <div class="enterdata">
                <h2 style="margin-top: 5px;">Mandatory settings</h2>
                <table summary="" border="0" cellpadding="0" cellspacing="0" style="margin: 0; width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="4"><h3 style="display:inline;">Admin password</h3> Enter a password for administrator account</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">&nbsp;</td>
                            <td style="width: 20%;">Username:</td>
                            <td style="width: 40%;">admin</td>
                            <td style="width: 20%;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">&nbsp;</td>
                            <td style="width: 20%;">Password:</td>
                            <td style="width: 40%;"><input type="password" name="adminpw1"  maxlength="100" value="<?php echo $_POST["adminpw1"]; ?>" /></td>
                            <td style="width: 20%;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">&nbsp;</td>
                            <td style="width: 20%;">Repeat password:</td>
                            <td style="width: 40%;"><input type="password" name="adminpw2"  maxlength="100" value="<?php echo $_POST["adminpw2"]; ?>" /></td>
                            <td style="width: 20%;">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                <table summary="" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="4"><h3 style="display:inline;">View</h3> Select the view (a view is the combination and position of the components displayed).</td>
                        </tr>
                        <tr>
                            <td style="width: 25%;">
                                <input type="radio" name="view" value="default" id="view_default" checked="checked" /><span class="label" onclick="o=document.getElementById('view_default');if(!o.checked)o.checked=true;">Default view</span>
                                <div class="small padleft">Substitution table on the right, content on the left, headline at the top, ticker at the bottom</div>
                            </td>
                            <td style="width: 25%; color: gray;">
                                <input type="radio" name="view" value="defaultnotop" id="view_defaultnotop" disabled="disabled" /><span class="label">Default w/o top</span>
                                <div class="small padleft">Substitution table on the right, content on the left, ticker at the bottom</div>
                                <div class="small padleft" style="font-style:italic;">Not available yet.</div>
                            </td>
                            <td style="width: 25%; color: gray;">
                                <input type="radio" name="view" value="dualcontent" id="view_dualcontent" disabled="disabled" /><span class="label">Dual content</span>
                                <div class="small padleft">Content (1) on the left, content (2) on the right, headline at the top, ticker at the bottom</div>
                                <div class="small padleft" style="font-style:italic;">Not available yet.</div>
                            </td>
                            <td style="width: 25%; color: gray;">
                                <input type="radio" name="view" value="dualcontentnotop" id="view_dualcontentnotop" disabled="disabled" /><span class="label">Dual content w/o top</span>
                                <div class="small padleft">Content (1) on the left, content (2) on the right, headline at the top, ticker at the bottom</div>
                                <div class="small padleft" style="font-style:italic;">Not available yet.</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="color: gray;">
                                <input type="radio" name="view" value="custom" id="view_custom" disabled="disabled" /><span class="label">Custom view</span>: <span class="small">Create a custom view by selecting where which components should be placed.</span>
                                <div class="small padleft" style="font-style: italic;">Not available yet.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="enterdata">
                <script type="text/javascript">
                var shown=false;
                function toggleDVT() {
                    if (shown) {
                        document.getElementById("dvt").style.display = "none";
                        document.getElementById("togglelink").firstChild.nodeValue = "[Show default values table]";
                        shown = false;
                    }
                    else {
                        document.getElementById("dvt").style.display = "table";
                        document.getElementById("togglelink").firstChild.nodeValue = "[Hide default values table]";
                        shown = true;
                    }
                }
                </script>
                <div style="float:right;font-size:80%;padding-top:5px;padding-right:20px;"><a href="javascript:toggleDVT();" id="togglelink">[Show default values table]</a></div>
                <h2 style="margin-top: 5px;">Default values</h2>
                <div><b>Important:</b> Only change these values if you know what you are doing! They will not be checked for correctness and thus can seriously break your installation!</div>
                <table summary="" border="0" cellpadding="0" cellspacing="0" id="dvt" style="display: none;">
                    <tbody>
                        <tr>
                            <th style="width: 25%">Setting</th><th style="width: 25%;">Value</th><th style="width: 50%;">Comment/Description</th>
                        </tr>
<?php include ("defaultvalues.php"); 
    foreach ($DV as $key => $values) { ?>
                        <tr>
                            <td colspan="3" style="font-style: italic; padding-left: 3em;"><?php lang_echo("DV_$key"); ?></td>
                        </tr>
<?php   foreach ($values as $value) { ?>
                        <tr>
                            <td><?php echo $value["name"]; ?></td>
                            <td><input type="text" name="dvt;<?php echo $key . ";" . $value["name"] ?>" value="<?php echo $value["value"]; ?>" style="width: 90%; text-align: right;" /></td>
                            <td><?php echo $value["comment"]; ?></td>
                        </tr>
<?php   }
    }?>
                    </tbody>
                </table>
            </div>
            <div>
                <table id="buttonbar" summary="" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td id="leftbox"><input type="button" name="back" value="< <?php lang_echo("genBack"); ?>" id="backbutton" onclick="this.form.action='?step=<?php echo ($step - 1); ?>'; this.form.submit();" /></td>
                            <td id="rightbox">
                                <input type="submit" name="next" value="<?php lang_echo("genNext"); ?> >" id="nextbutton" />
                            </td>
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