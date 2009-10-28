<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Installation script - creates necessary database tables and default entries
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

//TODO: install: create installation script

function makeNameValueTableQuery ($tablename) {
	return 
		"CREATE TABLE `$tablename` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`name` VARCHAR( 255 ) NOT NULL ,
		`value` VARCHAR( 255 ) NOT NULL
		)";
}


/*Create Table for user login data*/
$query = "CREATE TABLE `users` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`username` VARCHAR( 255 ) NOT NULL ,
`password` VARCHAR( 255 ) NOT NULL ,
`email` VARCHAR( 255 ) NOT NULL ,
`type` ENUM( 'admin', 'user' ) NOT NULL
)";

/*Create Table for global options*/
$query = makeNameValueTableQuery("globaloptions");
$query = "INSERT INTO `globaloptions`
            (`name`, `value`) 
            VALUES ('display_new_errors', 'admin');";

/*Create Table for global view options*/
$query = makeNameValueTableQuery("globalviewoptions");

/*Create Table for default view options*/
$query = makeNameValueTableQuery("view_default_view");

/*Create Table for ticker options*/
$query = makeNameValueTableQuery("com_tickers_options");
/*Create Table for ticker data*/
$query = "CREATE TABLE `com_tickers` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`caption` VARCHAR( 255 ) NOT NULL ,
`content` VARCHAR( 255 ) NOT NULL ,
`start` DATETIME NOT NULL ,
`end` DATETIME NOT NULL,
`deleted` BOOL NOT NULL
)";

/*Create Table for HTML page options*/
$query = makeNameValueTableQuery("com_htmlpages_options");
/*Create Table for HTML page data*/
$query = "CREATE TABLE `com_htmlpages` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`url` VARCHAR( 255 ) NOT NULL ,
`displaytime` INT NOT NULL,
`start` DATETIME NOT NULL ,
`end` DATETIME NOT NULL,
`deleted` BOOL NOT NULL
)";

/*Create Table for error log*/
$query = "CREATE TABLE `errors` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`date` DATETIME NOT NULL ,
`content` VARCHAR( 255 ) NOT NULL ,
`new` BOOL NOT NULL
)";

?>