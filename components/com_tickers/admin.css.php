<?php
/**
 * @version     2009-09-29
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      ticker manager (backend) style definitions
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

div#tickers div#tickermanNav {
    text-align: right;
}

div#tickers table#tickers {
    width: 100%;
    border-collapse: collapse;
}

div#tickers table#tickers th,
div#tickers table#tickers td {
    border: 1px solid black;
    padding: 1px 5px;
}

div#tickers table#tickers tr.none td {
    color: gray;
    font-style: italic;
    text-indent: 10px;
}

div#tickers table#tickers tr.category {
    font-style: italic;
    text-indent: 20px;
    font-weight: bold;
}

div#tickers table#tickers tr.past td {
    color: darkgray;
    font-style: italic;
}
div#tickers table#tickers tr.future td {
    color: darkblue;
    font-style: italic;
}

div#tickers table#tickers .tName {
    width: 130px;
    overflow: hidden;
}
div#tickers table#tickers .tContent {
    
}
div#tickers table#tickers .tFrom{
    width: 150px;
    text-align: center;
}
div#tickers table#tickers .tUntil {
    width: 150px;
    text-align: center;
}
div#tickers table#tickers .tEdit {
    width: 15px;
    text-align: center;
}
div#tickers table#tickers .tDelete {
    width: 15px;
    text-align: center;
}
div#tickers table#tickers .tRestore {
    width: 15px;
    text-align: center;
}
div#tickers table#tickers .tCheck {
    width: 20px;
    text-align: center;
}
div#tickers div#tickerListButtons {
    text-align: right;
    margin: 5px 0;
}

div#tickers div#tickerCreateTop {
    padding-top: 5px;
}
div#tickers form#tickerCreateRestartForm {
    float: right;
}

div#tickers table#tickerCreateContainerTable,
div#tickers table#tickerEditContainerTable {
    margin: 0 auto;
}

div#tickers form#tickerCreateForm {
    margin-top: 20px;
}

div#tickers div#tickerCreateButtonBar,
div#tickers div#tickerEditButtonBar {
    text-align: right;
}

div#tickers form input.captionInput {
    width: 200px;
}

div#tickers form input.contentInput {
    width: 99%;
}

div#tickers table.tickerCreateTable td.vertMiddle {
    vertical-align: middle;
}

div#tickers div#tickerDeleteTop {
    padding-top: 5px;
}
div#tickers div#tickerDeleteButtonBar {
    float: right;
}

div#tickers fieldset.tickerEdit table {
    width: 800px;
}
