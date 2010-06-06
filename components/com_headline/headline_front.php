<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      headline frontend
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
 
    defined("__MAIN") or die("Restricted access.");
    
    //include_once("../config/config.php");
    //include_once("../includes/database.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta http-equiv="refresh" content="10" />
    <meta name="author" content="Patrick Lehner" />

    <!-- <link rel="stylesheet" type="text/css" href="headline.css" /> -->
    
    <title>Headline</title>
    
    
    <style type="text/css"> /*Too lazy to pack this into an extra file :) */
    
    * {
        overflow: hidden;
    }
    
    html, body {
        height: 100%;
    }
    
    body {
        margin: 0;
        background: white;
        color: black;
        font-size: 16pt;
        font-family: Arial,Verdana,Helvetica,sans-serif,serif;
        text-align: left;
    }
    
    div#rightbox {
        float: right;
        padding: 0 10px;
    }
    
    span {
        padding-left: 20px;
    }
    
    </style>
    
    <script type="text/javascript">
    var d = <?php echo date("d");?>, m = <?php echo date("m");?>, y = <?php echo date("Y");?>;
    var h = <?php echo date("H");?>, n = <?php echo date("i");?>, s = <?php echo date("s");?>;
    
    window.onload = setInterval("tick()", 1000);

    function tick() {
        if (++s == 60) {
            if (++n == 60) {
                if (++h == 24) {
                    ++d;
                    h = 0;
                }
                n = 0;
            }
            s = 0;
        }
        var out;
        if (d < 10)
            out = "0" + d;
        else
            out = String(d);
        out += ".";
        if (m < 10)
            out += "0" + m;
        else
            out += String(m);
        out += "." + y + " ";
        if (h < 10)
            out += "0" + h;
        else
            out += String(h);
        out += ":";
        if (n < 10)
            out += "0" + n;
        else
            out += String(n);
        out += ":";
        if (s < 10)
            out += "0" + s;
        else
            out += String(s);
        document.getElementById("datetime").firstChild.nodeValue = out;
    }
    </script>

</head>
<body>
    <div id="rightbox">
        <span id="datetime"><?php echo date("d.m.Y H:i:s");?></span>
    </div>
    <div id="main"><span id="greeting">Theodor-Heuss-Gymnasium N&ouml;rdlingen</span></div>
</body>
</html>