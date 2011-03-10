<?php
/**
 * @version     2011-03-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010-2011 Patrick Lehner
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
 
defined("__MAIN") or die("Restricted access.");
class_exists("PluginLoginLogout") or die("Class 'PluginLoginLogout' is unknown [" . __FILE__ . "]");

/* @var $this PluginLoginLogout */

//the JS in this section is now included from an external file
CSSJSHandler::addScriptUrl($this->getWebPath() . "/js/LoginFieldsChecker.js");

?>

<form id="loginForm" action="?" method="post">
    <fieldset><legend><?php /*echo Lang::translate("Plugin_LoginLogout_LoginHeader");*/ ?>Login</legend>
        <table summary="" border="0" cellspacing="0" cellpadding="0">
            <tr id="errorrow" style="display:none;"><td colspan="2" style="color: red; text-align: center;">Please fill out all fields!</td></tr>
            <tr><td><div>Username:</div></td><td><input class="text" type="text" id="username" name="username" maxlength="255" /></td></tr>
            <tr><td><div>Password:</div></td><td><input class="text" type="password" id="pw" name="pw" maxlength="255" /></td></tr>
        </table>
        <input type="hidden" name="postview" value="login" />
        <input type="button" name="loginButton" value="Login" class="submit" onclick="if (checkLoginFields()) this.form.submit();" />
        <input type="submit" name="submitLoginForm" value="Login" style="display: none" />
        <div class="floatCleaner">&nbsp;</div>
    </fieldset>
</form>