<?php
/**
 * @version     2010-05-15
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

div#ComMgr table#comlist {
    width: 100%;
    clear: both;
}

div#ComMgr table#comlist tr.headingRow td {
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid black;
}

div#ComMgr table#comlist td {
    border-bottom: 1px solid gray;
    padding: 3px 5px;
    border-left: 1px solid #BBB;
}

div#ComMgr table#comlist td.check {
    padding: 1px;
    text-align: center;
    width: 15px;
}

div#ComMgr table#comlist td.enabled {
    width: 30px;
    text-align: center;
}

div#ComMgr table#comlist td.name {
}

div#ComMgr table#comlist td.comname {
}

div#ComMgr table#comlist td.installedAt {
    width: 150px;
    text-align: center;
}

div#ComMgr table#comlist td.id {
    width: 30px;
    text-align: right;
}

div#ComMgr table#comlist td.duplicate {
    width: 15px;
    text-align: center;
}

div#ComMgr table#comlist td.delete {
    width: 15px;
    text-align: center;
}

div#ComMgr table#comlist td:first-child {
    padding-left: 10px;
    border-left: 2px solid black;
}

div#ComMgr table#comlist td:last-child {
    padding-right: 10px;
    border-right: 2px solid black;
}

div#ComMgr table#comlist tr:first-child td {
    border-top: 2px solid black;
}

div#ComMgr table#comlist tr:last-child td {
    border-bottom: 2px solid black;
}

div#ComMgr table#comlist.rright tr:first-child td:last-child {
    -moz-border-radius-topright: 8px;           /*Firefox*/
    border-top-right-radius: 8px;               /*Opera/CSS3*/
}

div#ComMgr table#comlist.rright tr:last-child td:last-child {
    -moz-border-radius-bottomright: 8px;        /*Firefox*/
    border-bottom-right-radius: 8px;            /*Opera/CSS3*/
}

div#ComMgr table#comlist.rleft tr:first-child td:first-child {
    -moz-border-radius-topleft: 8px;           /*Firefox*/
    border-top-left-radius: 8px;               /*Opera/CSS3*/
}

div#ComMgr table#comlist.rleft tr:last-child td:first-child {
    -moz-border-radius-bottomleft: 8px;        /*Firefox*/
    border-bottom-left-radius: 8px;            /*Opera/CSS3*/
}

div#ComMgr form#duplicateForm table#comlist td:not(:last-child) {
    width: 25%;
}

div#ComMgr table#comlist td.newdname input,
div#ComMgr table#comlist td.newiname input {
    width: 95%;
    margin: 0 auto;
    display: block;
}

div#ComMgr div.buttonbar {
    float: left;
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

div#ComMgr form#deleteForm table#delquestion {
    width: 80%;
    margin: 0 auto 10px;
}

div#ComMgr form#deleteForm table#delquestion td.buttoncell {
    width: 200px;
    text-align: center;
}