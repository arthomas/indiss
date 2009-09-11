<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
?>
<form id="loginForm" action="index.php" method="post">
                <fieldset><legend>Registrieren</legend>
                    <table summary="" border="0" cellspacing="0" cellpadding="0">
                        <tr><td><div>Username:</div></td><td><input class="text" type="text" name="username" maxlength="255" /></td></tr>
                        <tr><td><div>Password:</div></td><td><input class="text" type="password" name="pw" maxlength="255" /></td></tr>
                        <tr><td><div>Repeat<br />password:</div></td><td><input class="text" type="password" name="pw2" maxlength="255" /></td></tr>
                        <tr><td><div>Email:</div></td><td><input class="text" type="text" name="email" maxlength="255" /></td></tr>
                    </table>
                    <input type="hidden" name="task" value="register"/>
                    <input type="submit" name="submit" value="Register" class="submit" />
                    <div class="floatCleaner">&nbsp;</div>
                </fieldset>
            </form>