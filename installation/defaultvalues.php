<?php
/**
 * @version     2010-02-21
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
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
        array ( "display_new_errors",       "admin", "" )
    ),
    "global_view_options" => array (
        array ( "screenDimensionX",         "1920",  "" ),
        array ( "screenDimensionY",         "1080",  "" )
    ),
    "view_default_view" => array (
        array ( "topBarHeight",             "30",    "" ),
        array ( "bottomBarHeight",          "30",    "" ),
        array ( "leftMainColumnWidth",      "60%",   "" )
    ),
    "com_content_options" => array (
        array ( "default_display_time",     "120",   "" ),
        array ( "error_display_time",       "30",    "" ),
        array ( "max_width",                "auto",  "" ),
        array ( "max_height",               "auto",  "" )
    ),
    "com_substtable_options" => array (
        array ( "html_options_path",        "/cli_scripts",                                 "not sure what this was supposed to do :/" ),
        array ( "display_time",             "15",                                           "time in seconds for which one page is displayed" ),
        array ( "max_lines",                "27",                                           "Maximum number of lines per page, including headers" ),
        array ( "start_lines",              "3",                                            "Lines of header on each page" ),
        array ( "html_template",            "/cli_scripts/substtable_template.html",        "" ),
        array ( "default_output_dir",       "/cli_scripts",                                 "" ),
        array ( "default_temp_dir",         "/temp/convert_substtable",                     "" ),
        array ( "color_today_even",         "#FFFFDD",                                      "" ),
        array ( "color_today_odd",          "#FFF3AE",                                      "" ),
        array ( "color_today_new",          "yellow",                                       "" ),
        array ( "color_tomorrow_even",      "#DDFFDD",                                      "" ),
        array ( "color_tomorrow_odd",       "#BEFFBE",                                      "" ),
        array ( "color_other_day_even",     "#DDEEFF",                                      "" ),
        array ( "color_other_day_odd",      "#D4E5F6",                                      "" ),
        array ( "error_display_time",       "10",                                           "" ),
        array ( "highlight_changes_after",  "07:00:00",                                     "" ),
        array ( "trim_times",               "",                                             "" )
    ),
    "com_tickers_options" => array ()
);


$keys = array ( "name", "value", "comment" );

foreach ( $DV as $key => $values ) {
    foreach ( $values as $key2 => $value ) {
        $DV[$key][$key2] = array_combine($keys, $value);
    }
}

//var_dump($DV);

?>