<?php
/**
 * @version     2010-08-13
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
 
define("__MAIN", 1);

require_once("../loaders/loader_minimal.php");

header("CONTENT-TYPE: text/css");

?>

/*Global style definitions*/

body {
    background: white;
    color: black;
    font-family: Arial, Verdana, Helvetica, sans-serif;
    font-size: 11pt;
    padding-top: 30px;
}

a[href] {
    color: #00B;
}

a:visited {
    color: #57B;
}

a:hover, a:focus {
    color: #35E;
}

input[type="button"].likeLink,
input[type="submit"].likeLink {
    -moz-appearance: none;
    background: white;
    border: 0 none;
    font-size: 11pt;
    color: #00B;
    font-family: Arial,Verdana,Helvetica,sans-serif;
}

input[type="button"].likeLink:hover,
input[type="button"].likeLink:focus,
input[type="submit"].likeLink:hover,
input[type="submit"].likeLink:focus {
    color: #35E;
}

div.floatCleaner {
    clear: both;
    height: 1px;
    max-height: 1px;
    border: 0 none;
    background: transparent;
    font-size: 0 !important;
}

div#topBar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    font-size: 9pt;
    background: white;
}

div#topBarInner {
    border: 0 none;
    border-bottom: 1px solid black;
    padding: 0 0 0 5px;
}

div#topBarInner div {
    padding: 5px 0 4px;
}

div#topBarInner div.topBarLeft {
    float: left;
    padding: 5px 6px 4px;
    border-right: 1px solid black;
}

div#topBarInner div#topBarSitename {
    font-weight: bold;
}

div.topBarRight {
    float: right;
    margin-left: 20px;
}

div#topBarInner div#topBarLang {
    padding: 2px 0 2px;
    font-size: 9pt;
}

div#topBarInner div#topBarLang select {
    font-size: 9pt;
}

div#topBarInner div#topBarLogout {
    padding: 0;
    margin-left: 10px;
    border-left: 1px solid black;
}

div#topBarInner div#topBarLogout input[type="submit"] {
    padding: 4px 10px 3px;
    font-size: 9pt;
}

div#topBarInner div#topBarLogout input[type="submit"]:hover {
    background: #EEE;
}

div#footer {
    clear: both;
    font-size: 80%;
    text-align: center;
    margin-top: 30px;
}


/*Live message log output*/

div.messagebox {
    width: 80%;
    margin: 0 auto 20px;
}

div.messagebox table {
    border-collapse: collapse;
    width: 100%;
}

div.messagebox table td {
    border: 1px solid gray;
}

div.messagebox table td.origin {
    border-right: 0 none;
    padding: 10px 0 10px 20px;
    vertical-align: top;
}

div.messagebox table td.message {
    border-left: 0 none;
    padding: 10px 20px 10px 10px;
    vertical-align: top;
}


/*Common plugin style definitions*/

div#output table.fwTable {
    width: 100%;
    clear: both;
}

div#output table.fwTable tr.headingRow td {
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid black;
}

div#output table.fwTable td {
    border-bottom: 1px solid gray;
    padding: 3px 5px;
    border-left: 1px solid #BBB;
}

div#output table.fwTable td:first-child {
    padding-left: 10px;
    border-left: 2px solid black;
}

div#output table.fwTable td:last-child {
    padding-right: 10px;
    border-right: 2px solid black;
}

div#output table.fwTable tr:first-child td {
    border-top: 2px solid black;
}

div#output table.fwTable tr:last-child td {
    border-bottom: 2px solid black;
}

div#output table.rright tr:first-child td:last-child {
    -moz-border-radius-topright: 8px;           /*Firefox*/
    border-top-right-radius: 8px;               /*Opera/CSS3*/
}

div#output table.rright tr:last-child td:last-child {
    -moz-border-radius-bottomright: 8px;        /*Firefox*/
    border-bottom-right-radius: 8px;            /*Opera/CSS3*/
}

div#output table.rleft tr:first-child td:first-child {
    -moz-border-radius-topleft: 8px;           /*Firefox*/
    border-top-left-radius: 8px;               /*Opera/CSS3*/
}

div#output table.rleft tr:last-child td:first-child {
    -moz-border-radius-bottomleft: 8px;        /*Firefox*/
    border-bottom-left-radius: 8px;            /*Opera/CSS3*/
}

div#output div.buttonbar {
    float: left;
}

div#output div.buttonbar table td {
    padding: 0;
    border-left: 1px solid gray;
}

div#output div.buttonbar table td:last-child:hover,
div#output div.buttonbar table td:last-child:hover input[type="button"],
div#output div.buttonbar table td input[type="button"]:hover,
div#output div.buttonbar table td input[type="button"]:focus {
    color: #35E;
    background: #EEE;
}

div#output div.buttonbar#buttonbarTop table td {
    border-top: 2px solid black;
}

div#output div.buttonbar#buttonbarTop table td:first-child {
    border-left: 2px solid black;
    -moz-border-radius-topleft: 8px;        /*Firefox*/
    border-top-left-radius: 8px;            /*Opera/CSS3*/
    padding: 2px 5px 2px 8px;
}

div#output div.buttonbar#buttonbarTop table td:last-child {
    border-right: 2px solid black;
    -moz-border-radius-topright: 8px;       /*Firefox*/
    border-top-right-radius: 8px;           /*Opera/CSS3*/
    padding-right: 4px;
}

div#output div.buttonbar#buttonbarBottom table td {
    border-bottom: 2px solid black;
}

div#output div.buttonbar#buttonbarBottom table td:first-child {
    border-left: 2px solid black;
    -moz-border-radius-bottomleft: 8px;     /*Firefox*/
    border-bottom-left-radius: 8px;         /*Opera/CSS3*/
    padding: 2px 5px 2px 8px;
}

div#output div.buttonbar#buttonbarBottom table td:last-child {
    border-right: 2px solid black;
    -moz-border-radius-bottomright: 8px;    /*Firefox*/
    border-bottom-right-radius: 8px;        /*Opera/CSS3*/
    padding-right: 4px;
}

