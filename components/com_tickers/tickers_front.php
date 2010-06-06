<?php
/**
 * @version     2010-05-19
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      tickers_front -- Display tickers in the frontend
 * 
 * @note        The JavaScript for the scrolling ticker is based off one of the scripts from www.dynmicdrive.com .
 *              Even though I modified it so heavily (hardly any of the JavaScript is actually left) that I didn't
 *              include the credit note, I want to give them credit here.
 *              The idea in itself isn't _THAT_ great (else they should have a patent on it :) The script simply
 *              moves a DIV container a certain number of pixels ever so-and-so milliseconds (that's about all that
 *              JS will let you do), so I changed the function names a little and hope no one's gonna sue me ^^
 *              'nuff said :)
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
    
    include_once("config/config.php");
    include_once("includes/database.php");
    
    
    //TODO: ticker: read ticker parameters from database
    //ticker variables:
    $tickerMoveSpeed = 1; //in px/tick
    $tickerTickSpeed = 10; //in ms
    $tickerRefreshSpeed = 30; //in sec
    
    $tickerSeparator = " +++ ";
    
    
    $query = "SELECT * FROM `com_tickers`";

    $result = mysql_query($query);
    
    if (!$result) { 
        $message .= sprintf($_LANG["errDBError"] . "<br />\n", mysql_error());      //  <<-----  $_LANG
        $message .= sprintf($_LANG["errDBErrorQry"] . "<br />\n", $query);          //  <<-----  $_LANG
    } else {
        unset($rows); //just to be sure
        while ($row = mysql_fetch_object($result)) { //fetch all resulting rows
            $rows[] = $row;  //and save them into our array
        }
        
        unset($tickers); //just to be sure
        if ( !empty( $rows ) )
            foreach ($rows as $value) {
                if (!($value->deleted) && (strtotime($value->end) > time()) && (strtotime($value->start) < time())) {
                    if (!empty($value->caption) && !empty($value->content))
                        $tickers[] = "<span class=\"caption\">" . $value->caption . ":</span> " . $value->content;
                    else if (!empty($value->caption))
                        $tickers[] = "<span class=\"caption\">" . $value->caption . "</span>";
                    else if (!empty($value->content))
                        $tickers[] = $value->content;
                }
            }
        if ( empty( $tickers ) )
            $tickers[] = "Momentan sind keine Ticker vorhanden";                                        //TODO: ticker: include _LANG for "no tickers found" msg
        $tickerContent = $tickerSeparator . implode($tickerSeparator, $tickers) . $tickerSeparator;     //TODO: ticker: make separator customizable
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta http-equiv="refresh" content="300; URL=?frame=bottom" /><?php //TODO: ticker: make refresh time customizable ?> 
    <meta name="author" content="Patrick Lehner" />

    <!-- <link rel="stylesheet" type="text/css" href="ticker.css" /> -->
    
    <title>Scrolling ticker</title>
    
    
    <style type="text/css"> /*Too lazy to pack this into an extra file :) */
    
    * {
        overflow: hidden;
    }
    
    body {
        margin: 0;
        background: white;
        color: black;
        font-size: 16pt;
        font-family: Arial,Verdana,Helvetica,sans-serif,serif;
    }
    
    div#blind {
        visibility: hidden;
        position: absolute;
        top: -100px;
        left: -10000px;
        white-space: nowrap;
    }
    
    div#ticker {
        white-space: nowrap;
        position: absolute;
    }
    
    span.caption {
        font-weight: bold;
    }
    
    </style>
    
    
    <script type="text/javascript" language="JavaScript1.2">

    var ticker;
    var tickerWidth;
    
    function initialize () {
        ticker = document.getElementById("ticker");                                         //throw the reference to the main DIV into a variable to speed things up
        ticker.style.left = document.getElementsByTagName("body")[0].offsetWidth + "px";    //let the ticker start to the right of the screen
        tickerWidth = document.getElementById("blind").offsetWidth;                         //remember the width of the ticker element (retrieved via "blind" element)
    	tick = setInterval("scrollTicker()", <?php echo $tickerTickSpeed; ?>);
    }
    
    window.onload = initialize;
    
    function scrollTicker() {           //this is our worker function
        posX = parseInt(ticker.style.left);
        if (posX > (-tickerWidth))
            ticker.style.left = posX - <?php echo $tickerMoveSpeed; ?> + "px";
        else
            ticker.style.left = document.getElementsByTagName("body")[0].offsetWidth + "px";
    }
    </script>


</head>
<body>
    <div id="blind"><?php echo $tickerContent; ?></div>
    <div id="tickerOuter">
        <div id="tickerContainer">
            <div id="ticker"><?php echo $tickerContent; ?></div>
        </div>
    </div>
</body>
</html>