<?php
/**
 * @version     2009-11-06
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

    define("__INSTALL", 1);
    
    $defaultlang = "en";

    $dbhost        = "localhost";
    $dbuser        = "root";
    $dbpass        = "";
    $dbname        = "infoscreen";
    
    if ( !empty( $_POST["lang"] ) ) {
        $lang = $_POST["lang"];
    } else {
        $lang = $defaultlang;
    }
    
    include ( "lang/lang.php");
    
    $configfile    = true;  //need this to cheat database.php
    
    include_once("../includes/database.php");
    
    if ( empty( $_GET["step"] ) ) {
        $step = 1;
    } else {
        $step = $_GET["step"];
    }
    
    include ("page" . $step . ".php");
    
    
    if ( $THEEND ) {
    
        unset( $errors );
    
        function makeNameValueTableQuery ( $tablename ) {
            return 
                "CREATE TABLE `$tablename` (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `name` VARCHAR( 255 ) NOT NULL ,
                `value` VARCHAR( 255 ) NOT NULL ,
                `comment` VARCHAR( 255 ) NOT NULL
                )";
        }
    
    
        /*Create Table for user login data*/
        $query = 
            "CREATE TABLE `users` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `username` VARCHAR( 255 ) NOT NULL ,
            `password` VARCHAR( 255 ) NOT NULL ,
            `email` VARCHAR( 255 ) NOT NULL ,
            `type` ENUM( 'admin', 'user' ) NOT NULL
            )";
        db_commit2( $query, $errors );
    
        /*Create Table for global options*/
        $query = makeNameValueTableQuery("global_options");
        db_commit2( $query, $errors );
        $query = 
            "INSERT INTO `global_options`
            (`name`, `value`) 
            VALUES
            ('display_new_errors', 'admin')";
        db_commit2( $query, $errors );
    
        /*Create Table for global view options*/
        $query = makeNameValueTableQuery("global_view_options");
        db_commit2( $query, $errors );
    
        /*Create Table for default view options*/
        $query = makeNameValueTableQuery("view_default_view");
        db_commit2( $query, $errors );
    
        /*Create Table for ticker options*/
        $query = makeNameValueTableQuery("com_tickers_options");
        db_commit2( $query, $errors );
        /*Create Table for ticker data*/
        $query = 
            "CREATE TABLE `com_tickers` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `caption` VARCHAR( 255 ) NOT NULL ,
            `content` VARCHAR( 255 ) NOT NULL ,
            `start` DATETIME NOT NULL ,
            `end` DATETIME NOT NULL,
            `enabled` BOOL NOT NULL,
            `deleted` BOOL NOT NULL
            )";
        db_commit2( $query, $errors );
    
        /*Create Table for content options*/
        $query = makeNameValueTableQuery("com_content_options");
        db_commit2( $query, $errors );
        $query =
            "INSERT INTO `com_content_options`
            (`name`, `value`)
            VALUES
            ('default_display_time', 120),
            ('error_display_time', 10),
            ('max_width', 'auto'),
            ('max_height', 'auto')";
        db_commit2( $query, $errors );
        /*Create Table for content data*/
        $query = 
            "CREATE TABLE `com_content` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `name` VARCHAR( 255 ) NOT NULL ,
            `url` VARCHAR( 255 ) NOT NULL ,
            `displaytime` INT NOT NULL,
            `start` DATETIME NOT NULL ,
            `end` DATETIME NOT NULL,
            `enabled` BOOL NOT NULL,
            `deleted` BOOL NOT NULL
            )";
        db_commit2( $query, $errors );
    
        /*Create Table for error log*/
        $query = 
            "CREATE TABLE `errors` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `date` DATETIME NOT NULL ,
            `content` VARCHAR( 255 ) NOT NULL ,
            `new` BOOL NOT NULL
            )";
        db_commit2( $query, $errors );
        
        /*Create Table for substitution table options*/
        $query = makeNameValueTableQuery("com_substtable_options");
        db_commit2( $query, $errors );
        
        /*Create Table for headline options*/
        $query = makeNameValueTableQuery("com_headline_options");
        db_commit2( $query, $errors );
    
    }

?>