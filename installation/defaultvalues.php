<?php
/**
 * @version     2009-11-26
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Array containing the default values for various settings, used in installation script
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


// DO NOT CHANGE THESE VALUES UNLESS YOU KNOW WHAT YOU ARE DOING!!!


$DV = array (
    "global_options" => array (
        array ( "name" => "display_new_errors",     "value" => "admin", "comment" => "" )
    ),
    "global_view_options" => array (
        array ( "name" => "screenDimensionX",       "value" => "1920",  "comment" => "" ),
        array ( "name" => "screenDimensionY",       "value" => "1080",  "comment" => "" )
    ),
    "view_default_view" => array (
        array ( "name" => "topBarHeight",           "value" => "30",    "comment" => "" ),
        array ( "name" => "bottomBarHeight",        "value" => "30",    "comment" => "" ),
        array ( "name" => "leftMainColumnWidth",    "value" => "60%",   "comment" => "" )
    ),
    "com_content_options" => array (
        array ( "name" => "default_display_time",   "value" => "120",   "comment" => "" ),
        array ( "name" => "error_display_time",     "value" => "30",    "comment" => "" ),
        array ( "name" => "max_width",              "value" => "auto",  "comment" => "" ),
        array ( "name" => "max_height",             "value" => "auto",  "comment" => "" )
    )
);
 
    

?>