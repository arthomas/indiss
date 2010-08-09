<?php
/**
 * @version     2010-08-09
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
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

//the JS in this section should actually be inserted with the CSSJSHandler, but that isnt implemented yet
CSSJSHandler::addStyleUrl($this->getFullPath() . "/js/LoginFieldsChecker.js");

?>

<script type="text/javascript">
function checkLoginFields() {
	
}
</script>

<form id="loginForm" action="" method="post">
    <fieldset><legend><?php /*echo Lang::translate("Plugin_LoginLogout_LoginHeader");*/ ?>Login</legend>
        <table summary="" border="0" cellspacing="0" cellpadding="0">
            <?php if ($usernamemissing || $passwordmissing) { ?><tr><td colspan="2" style="color: red; text-align: center;">Please fill out all fields!</td></tr><?php } ?> 
            <tr><td><div>Username:</div></td><td><input class="text" type="text" name="username" maxlength="255" value="<?php echo $_POST['username']; ?>" <?php if ($usernamemissing) {echo 'style="background: #FBB;"';} ?> /></td></tr>
            <tr><td><div>Password:</div></td><td><input class="text" type="password" name="pw" maxlength="255" value="<?php echo $_POST['pw']; ?>" <?php if ($passwordmissing) {echo 'style="background: #FBB;"';} ?> /></td></tr>
        </table>
        <input type="hidden" name="task" value="login" />
        <input type="submit" name="submit" value="Login" class="submit" />
        <div class="floatCleaner">&nbsp;</div>
    </fieldset>
</form>