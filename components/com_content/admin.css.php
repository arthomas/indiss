<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      HTML pages manager (backend) style definitions
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

div#content div#contentNav {
    text-align: right;
}

div#content table#contentTable {
    width: 100%;
    border-collapse: collapse;
}

div#content table#contentTable th {
    border: 1px solid black;
    overflow: hidden;
    padding: 1px;
}

div#content table#contentTable td {
    border: 1px solid black;
    overflow: hidden;
    padding: 1px;
}

div#content table#contentTable .tName {
    width: 250px;
}

div#content table#contentTable .tType {
    width: 100px;
    text-align: center;
}

div#content table#contentTable .tDispTime {
    width: 100px;
    text-align: center;
}

div#content table#contentTable .tFrom,
div#content table#contentTable .tUntil {
    width: 150px;
    text-align: center;
}

div#content table#contentTable .tEdit,
div#content table#contentTable .tDelete,
div#content table#contentTable .tRestore {
    width: 15px;
    text-align: center;
}

div#content table#contentTable .tCheck {
    width: 20px;
    text-align: center;
}

div#content table#contentTable tr.category {
    font-style: italic;
    text-indent: 20px;
    font-weight: bold;
}

div#content div#contentListButtons {
    text-align: right;
    margin: 5px 0;
}


div#content div#contentCreateTop {
    margin-top: 10px;
}

div#content div#contentCreateTop form {
    float: right;
}

div#content table#contentCreateContainerTable {
    margin: 0 auto;
}

div#content fieldset.contentCreateBox span.createBoxTypeInfo {
    margin-left: 20px;
    font-size: 80%;
    vertical-align: baseline;
    font-style: italic;
}


div#content form#contentCreateForm {
    margin-top: 20px;
}

div#content form#contentCreateForm div#contentCreateButtonBar {
    text-align: right;
}

div#content form#contentCreateForm input.nameInput {
    width: 200px;
}

div#content form#contentCreateForm input.URLInput {
    width: 99%;
}

div#content form#contentCreateForm input.timeInput {
    text-align: right;
    width: 40px;
}

div#content table.contentCreateTable tr.TypeLine td {
    padding-bottom: 10px;
}

div#content table.contentCreateTable td.vertMiddle {
    vertical-align: middle;
}

div#content table.contentCreateTable div.FileButtons {
    float: right;
}

div#content table.contentDateTable td {
    padding: 1px 2px 0;
}