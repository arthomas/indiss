<?php
/**
 * @version     2010-04-12
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      component manager UI (backend) style definitions
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
 
    define("__MAIN", 1);

    include_once("../../config/config.php");
    include_once("../../includes/database.php");

    header("CONTENT-TYPE: text/css");
    
?>

div#ComMgr div#subnav {
    float: right;
    margin: 20px 0px;
    font-size: 0;
}

div#ComMgr div#subnav a {
    padding: 4px 5px;
    margin: 0;
    border: 1px solid black;
    border-right: none;
    font-size: 11pt;
}

div#ComMgr div#subnav a:hover {
    background: #EEE;
}

div#ComMgr div#subnav a:first-child {
    -moz-border-radius-topleft: 6px;        /*Firefox*/
    -moz-border-radius-bottomleft: 6px;     /*Firefox*/
    border-top-left-radius: 6px;            /*Opera/CSS3*/
    border-bottom-left-radius: 6px;         /*Opera/CSS3*/
    padding-left: 8px;
}

div#ComMgr div#subnav a:last-child {
    border-right: 1px solid black;
    -moz-border-radius-topright: 6px;       /*Firefox*/
    -moz-border-radius-bottomright: 6px;    /*Firefox*/
    border-top-right-radius: 6px;           /*Opera/CSS3*/
    border-bottom-right-radius: 6px;        /*Opera/CSS3*/
    padding-right: 8px;
}

div#ComMgr table#userlist {
    width: 100%;
}

div#ComMgr table#userlist tr.headingRow td {
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid black;
}

div#ComMgr table#userlist td {
    border-bottom: 1px solid gray;
    padding: 3px 5px;
}

div#ComMgr table#userlist td.check {
    padding: 1px;
    text-align: center;
    width: 15px;
}

div#ComMgr table#userlist td.enabled {
    width: 30px;
    text-align: center;
    border-left: 1px solid #BBB;
}

div#ComMgr table#userlist td.name {
    border-left: 1px solid #BBB;
}

div#ComMgr table#userlist td.comname {
    border-left: 1px solid #BBB;
}

div#ComMgr table#userlist td.installedAt {
    width: 150px;
    text-align: center;
    border-left: 1px solid #BBB;
}

div#ComMgr table#userlist td.id {
    width: 30px;
    text-align: right;
    border-left: 1px solid #BBB;
}

div#ComMgr table#userlist td.clone {
    width: 15px;
    text-align: center;
    border-left: 1px solid #BBB;
}

div#ComMgr table#userlist td.delete {
    width: 15px;
    text-align: center;
    border-left: 1px solid #BBB;
}

div#ComMgr table#userlist td:first-child {
    padding-left: 10px;
    border-left: 2px solid black;
}

div#ComMgr table#userlist td:last-child {
    padding-right: 10px;
    border-right: 2px solid black;
}

div#ComMgr table#userlist tr:first-child td {
    border-top: 2px solid black;
}

div#ComMgr table#userlist tr:last-child td {
    border-bottom: 2px solid black;
}

div#ComMgr table#userlist tr:first-child td:last-child {
    -moz-border-radius-topright: 8px;           /*Firefox*/
    border-top-right-radius: 8px;               /*Opera/CSS3*/
}

div#ComMgr table#userlist tr:last-child td:last-child {
    -moz-border-radius-bottomright: 8px;        /*Firefox*/
    border-bottom-right-radius: 8px;            /*Opera/CSS3*/
}

div#ComMgr div.buttonbar {
}

div#ComMgr div.buttonbar table td {
    padding: 0;
    border-left: 1px solid gray;
}

div#ComMgr div.buttonbar table td:last-child:hover,
div#ComMgr div.buttonbar table td:last-child:hover input[type="button"],
div#ComMgr div.buttonbar table td input[type="button"]:hover,
div#ComMgr div.buttonbar table td input[type="button"]:focus {
    color: #35E;
    background: #EEE;
}

div#ComMgr div.buttonbar#buttonbarTop table td {
    border-top: 2px solid black;
}

div#ComMgr div.buttonbar#buttonbarTop table td:first-child {
    border-left: 2px solid black;
    -moz-border-radius-topleft: 8px;        /*Firefox*/
    border-top-left-radius: 8px;            /*Opera/CSS3*/
    padding: 2px 5px 2px 8px;
}

div#ComMgr div.buttonbar#buttonbarTop table td:last-child {
    border-right: 2px solid black;
    -moz-border-radius-topright: 8px;       /*Firefox*/
    border-top-right-radius: 8px;           /*Opera/CSS3*/
    padding-right: 4px;
}

div#ComMgr div.buttonbar#buttonbarBottom table td {
    border-bottom: 2px solid black;
}

div#ComMgr div.buttonbar#buttonbarBottom table td:first-child {
    border-left: 2px solid black;
    -moz-border-radius-bottomleft: 8px;     /*Firefox*/
    border-bottom-left-radius: 8px;         /*Opera/CSS3*/
    padding: 2px 5px 2px 8px;
}

div#ComMgr div.buttonbar#buttonbarBottom table td:last-child {
    border-right: 2px solid black;
    -moz-border-radius-bottomright: 8px;    /*Firefox*/
    border-bottom-right-radius: 8px;        /*Opera/CSS3*/
    padding-right: 4px;
}