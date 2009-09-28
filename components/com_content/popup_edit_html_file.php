<?php
/**
 * @version     2009-09-26
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
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
    
    session_name("InfoScreenAdmin");
    session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />
    
    <title>Create file</title>
    
    <script type="text/javascript" src="<?php echo $basepath; ?>/ckeditor/ckeditor.js"></script>
    
    <style type="text/css">
        fieldset {
            border: 1px solid black;
            margin: 10px;
            padding: 10px;
        }
        
        div#buttonContainer {
            float:right;
        }
    </style>
    
</head>
<?php if ( empty( $_SESSION["username"] ) ) { //if the user is not logged in ?>
<body>
    Fehler: Sie sind nicht eingeloggt.
</body>
<?php } else  //else: the user is logged in
      if ( !isset( $_GET["index"] ) ) { //the index is required to write the file path back into the create form ?>
<body>
    Fehler: Es wurden nicht alle notwendigen Parameter &uuml;bergeben.
</body>
<?php } else if ( isset( $_POST["submit"] ) ) { 
        if ( empty( $_POST["htmlContent"] ) || empty( $_POST["filename"] ) ) { ?>
<body>
    <?php if ( empty( $_POST["htmlContent"] ) ) { ?>Fehler: Das Dokument war leer.<br /><?php } ?>
    <?php if ( empty( $_POST["filename"] ) ) { ?>Fehler: Kein Dateiname angegeben.<br /><?php } ?>  
    <div id="buttonContainer">
        <form>
            <input type="button" value="Zur&uuml;ck" onclick="history.back();" />
            <input type="button" value="Abbrechen" onclick="window.close();" />
        </form>
    </div>
</body>
<?php   } else {
            $result = file_put_contents($_SERVER["DOCUMENT_ROOT"] . "$basepath/upload/" . $_POST["filename"], $_POST["htmlContent"]);
            if ( !$result ) { //an error has occurred during saving ?>
<body>
    Fehler: Beim Speichern der Datei ist ein Fehler aufgetreten.<br />
    Debug: Path: <?php echo $_SERVER["DOCUMENT_ROOT"] . "$basepath/upload/" . $_POST["filename"]; ?><br />
    Result: <?php echo ($result === false) ? 'false' : $result; ?><br />
    Content: <pre><?php echo $_POST["htmlContent"]; ?></pre>
    <div id="buttonContainer">
        <form>
            <input type="button" value="Zur&uuml;ck" onclick="history.back();" />
            <input type="button" value="Abbrechen" onclick="window.close();" />
        </form>
    </div>
</body>
<?php       } else { ?>
<body onload="opener.document.getElementById('URL<?php echo $_GET["index"]; ?>').value='<?php echo "$basepath/upload/" . $_POST["filename"]; ?>'">
    <fieldset><legend>Datei hochgeladen:</legend>
        <div>Pfad auf dem Server (URL): <?php echo "$basepath/upload/" . $_POST["filename"]; ?> </div>
        <div style="display: none;">Pfad auf dem Server (Dateisystem): <?php echo $_SERVER["DOCUMENT_ROOT"] . "$basepath/upload/" . $_POST["filename"] ?></div>
        <div>Dateigr&ouml;&szlig;e: <?php echo $result; ?> Bytes</div>
    </fieldset>
    <div id="buttonContainer">
        <form>
            <input type="button" value="Schlie&szlig;en" onclick="window.close();" />
        </form>
    </div>
</body>
<?php       }
        }
      } else { ?>
<body>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <fieldset><legend>Neue Seite erstellen</legend>
            <textarea name="htmlContent" id="htmlContent"></textarea>
            <script type="text/javascript">
                CKEDITOR.replace( 'htmlContent' );
            </script>
        </fieldset>
        <div id="bottomline">
            Dateiname: <input type="text" name="filename" value="newfile.html" />
            <div id="buttonContainer">
                <input type="submit" value="Speichern" name="submit" />
                <input type="button" value="Abbrechen" onclick="window.close();"/>
            </div>
        </div>
    </form>
</body>
<?php }?>
</html>