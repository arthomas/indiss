<?php
/**
 * @version     2010-04-01
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
 * @module      English language file for the User Manager
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
 
    $_LANG["usrmanUserManager"]             = "User manager";
    
    $_LANG["usrmanUnameAlreadyExists"]      = "A user named '%s' already exists"; //%s: username
    $_LANG["usrmanCreateUserDBError"]       = "Database error while creating user %s\nDatabase said: %s\nQuery: <pre>%s</pre>"; //%s: username; %s: DB error; %s: DB query
    $_LANG["usrmanGetDBInsertIDFail"]       = "Could not retrieve database entry ID";
    $_LANG["usrmanCreateUserSuccess"]       = "User '%s' successfully created"; //%s: username

?>