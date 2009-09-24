<?php
/**
 * @version     2009-09-24
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
 
    defined("__MAIN") or die("Restricted access.");
    
    include_once("../config/config.php");
    include_once("../includes/database.php");
    
    $query = "SELECT * FROM `com_content`";

    $result = mysql_query($query);
    
    if (!$result) { 
        $message .= sprintf($_LANG["errDBError"] . "<br />\n", mysql_error());      //  <<-----  $_LANG
        $message .= sprintf($_LANG["errDBErrorQry"] . "<br />\n", $query);          //  <<-----  $_LANG
    } else {
        unset($rows); //just to be sure
        while ($row = mysql_fetch_object($result)) { //fetch all resulting rows
            $rows[] = $row;  //and save them into our array
        }
        
        unset($items); //just to be sure
        if ( !empty( $rows ) )
            foreach ($rows as $value) {
                if (!($value->deleted) && (strtotime($value->end) > time()) && (strtotime($value->start) < time())) {
                    $items[] = $value;
                }
            }
            
        if ( empty( $items ) ) {
            $type = "none";
        } else {
            unset( $current );
            if ( !empty( $_GET["last"] ) ) {
                $stopnext = false;
                foreach ($items as $value)
                    if ( $value->id == $_GET["last"] ) {
                        $stopnext = true;
                        //continue;
                    } else if ($stopnext) {
                        $current = $value;
                        break;
                    }
                if ( !isset( $current ) )
                    $current = $items[0];
            } else
                $current = $items[0];
            
            switch ($current->type) {
                case "ExternalPage":
                case "LocalPage":
                case "ExternalOther":
                case "LocalOther":
                    $type = "iframe";
                    break;
                case "ExternalImage":
                case "LocalImage":
                    $type = "image";
                    break;
                default:
                    $type = "unknown";
                    break;
            }
        }
    }
    
    //set up the auto-reload string
    if ( empty( $type ) || ( $type == "unknown" ) || ( $type == "none" ) ) {
        $reload = getValueByNameD("com_content_options", "error_display_time", 30);
        $reload .= "; URL=left.php";                                                    //TODO: content: generalize self-call
        if ( $type == "unknown" )
            $reload .= "?last=" . $current->id;
    } else {
        $reload = $current->displaytime . "; URL=left.php?last=" . $current->id;
    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta http-equiv="refresh" content="<?php echo $reload; ?>" />
    <meta name="author" content="Patrick Lehner" />

    <!-- <link rel="stylesheet" type="text/css" href="content.css" /> -->
    
    <title>Content</title>
    
    
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
        text-align: center;
    }
    
    div#parent {
        width: 100%;
        height: 100%;
        display: table;
    }
    
    div#ContentContainer {
        display: table-cell;
        vertical-align: middle;
        text-align: center;
    }
    
    iframe {
        overflow: hidden;
    }
    
    img {
        text-align: center;
        vertical-align: middle;
        margin: auto;
    }
    
    </style>

</head>
<body>
    <div id="parent">
        <div id="ContentContainer">
<?php if ( $type == "none" ) { ?>
            Momentan ist kein Inhalt vorhanden
<?php }
else if ( $type == "iframe" ) { ?>
            <iframe src="<?php echo $current->url; ?>" scrolling="no" frameborder="0" width="100%" height="<?php echo getValueByNameD("com_content_options", "iframe_height", 1020); ?>"></iframe>
<?php } 
else if ( $type == "image" ) { 
    if ( $current->type == "LocalImage" )
        $size = getimagesize( $_SERVER["DOCUMENT_ROOT"] . $current->url );
    else
        $size = getimagesize( $current->url );
    if ( $size === false ) { ?>
            Beim holen der Bilddatei <pre><?php echo $current->url; ?></pre> ist ein Fehler aufgetreten. Bitte benachrichtigen Sie den Systembetreuer und/oder Entwickler.<br />
            <?php echo getcwd();?>
<?php }
    else {
        $maxwidth = getValueByNameD("com_content_options", "img_max_width", 1100);
        $maxheight = getValueByNameD("com_content_options", "img_max_height", 1000);
        if ( ( $size[0] > $maxwidth ) && ( $size[1] > $maxheight ) ) {
            if ( $size[0] > $size[1] ) {
                $width = $maxwidth;
                $height = ( $maxwidth / $size[0] ) * $size[1];
            } else {
                $height = $maxheight;
                $width = ( $maxheight / $size[1] ) * $size[0];
            }
        } else if ( $size[0] > $maxwidth ) {
            $width = $maxwidth;
            $height = ( $maxwidth / $size[0] ) * $size[1];
        } else if ( $size[1] > $maxheight ) {
            $height = $maxheight;
            $width = ( $maxheight / $size[1] ) * $size[0];
        } else {
            $width = $size[0];
            $height = $size[1];
        }?>
            <img src="<?php echo $current->url; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
<?php }
}
else { ?>
            Ein Fehler ist aufgetreten. Bitte benachrichtigen Sie den Systembetreuer und/oder Entwickler.
<?php } ?>
        </div>
    </div>
</body>
</html>