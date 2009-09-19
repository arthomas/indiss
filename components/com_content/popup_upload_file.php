<?php
/**
 * @version     2009-09-19
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

    include_once "../../config/config.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />
    
    <title>Upload file</title>
    
    <style type="text/css">
        fieldset {
            border: 1px solid black;
            margin: 10px;
            padding: 10px;
        }
    </style>
</head>
<?php   if ( !isset( $_GET["index"] ) ) { ?>
<body>
    Fehler: Es wurden nicht alle notwendigen Parameter &uuml;bergeben.
<?php   } else if ( isset( $_POST["submit"] ) ) {
            $destination ="../../upload/" . basename( $_FILES["file"]["name"] );
            if ( move_uploaded_file( $_FILES["file"]["tmp_name"], $destination ) ) { ?>
<body onload="opener.document.getElementById('URL<?php echo $_GET["index"]; ?>').value='<?php echo "$basepath/upload/" . basename( $destination ); ?>'">
    <fieldset><legend>Datei hochgeladen:</legend>
        <div>Hochgeladene Datei: <?php echo $_FILES["file"]["name"]; ?> </div>
        <div>Pfad auf dem Server (URL): <?php echo "$basepath/upload/" . basename( $destination ); ?> </div>
        <div style="display: none;">Pfad auf dem Server (Dateisystem): <?php echo $destination; ?></div>
        <div>Dateigr&ouml;&szlig;e: <?php echo filesize( $destination ); ?> Bytes</div>
<?php       } else { ?>
<body>
    <fieldset><legend>Datei hochgeladen:</legend>
            Ein Fehler ist aufgetreten.
<?php       } ?>
    </fieldset>
<?php } else { ?>
<body>
    <fieldset><legend>Datei hochladen:</legend>
        <div id="info">Bitte w&auml;hlen Sie die Datei aus, die Sie hochladen m&ouml;chten, und klicken Sie dann auf "Hochladen".</div>
        <form action="popup_upload_file.php?index=<?php echo $_GET["index"]; ?>" method="post" enctype="multipart/form-data">
            <div><input name="file" type="file" /></div>
            <div style="text-align: right;"><input type="submit" name="submit" value="Hochladen" /></div>
        </form>
    </fieldset>
<?php } ?>
</body>
</html>