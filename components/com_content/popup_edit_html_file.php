<?php
/**
 * @version     2010-07-01
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

//Wonder if it wont give a better overview if we pack all this intermittent HTML into
//PHP strings?

    include_once "../../config/config.php";
    
    session_name("INDISSAdmin");
    session_start();
    
    $template_basepath = $_SERVER["DOCUMENT_ROOT"] . $basepath . "/components/com_content/files/templates";
    $html_basepath =     $_SERVER["DOCUMENT_ROOT"] . $basepath . "/components/com_content/files/html";

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
<?php
    if ( empty( $_SESSION["username"] ) ) { //if the user is not logged in
        echo "<body>\n";
        echo "    Fehler: Sie sind nicht eingeloggt.\n";
        echo "</body>\n";
    } else  //else: the user is logged in
    if ( !isset( $_GET["index"] ) ) { //the index is required to write the file path back into the create/edit form
        echo "<body>\n";
        echo "    Fehler: Es wurden nicht alle notwendigen Parameter &uuml;bergeben.\n";
        echo "</body>\n";
    } else if ( isset( $_POST["submit"] ) ) {
        if ( isset( $_POST["template_box"] ) ) {
            
            $oldcontent = "";
            if ( $_POST["template_box"] == "editold" && !empty( $_GET["oldfile"] ) ) {
                if ( file_exists( $_SERVER["DOCUMENT_ROOT"] . $_GET["oldfile"] ) && ( preg_match('/\.(?:htm|html)$/', $_GET["oldfile"]) ) ) {
                    $oldcontent = file_get_contents($_SERVER["DOCUMENT_ROOT"] . $_GET["oldfile"]);
                }
            } else if ( $_POST["template_box"] != "createnew" ) {
                if ( $xml = simplexml_load_file($template_basepath . "/" . $_POST["template_box"]) ) {
                    if ( file_exists( $template_basepath . "/" . $xml->htmlfile ) ) {
                        $oldcontent = file_get_contents($template_basepath . "/" . $xml->htmlfile);
                    }
                }
            }
            echo "<body>\n";
            echo '    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">' . "\n";
            echo "        <fieldset><legend>Neue Seite erstellen</legend>\n";
            echo '            <textarea name="htmlContent" id="htmlContent">' . $oldcontent . "</textarea>\n";
            echo '            <script type="text/javascript">' . "\n";
            echo "                CKEDITOR.replace( 'htmlContent' );\n";
            echo "            </script>\n";
            echo "        </fieldset>\n";
            echo '        <div id="bottomline">'."\n";
            echo '            Dateiname: <input type="text" id="filename" name="filename" value="' . $_POST["filename"] . '" disabled="disabled" />'."\n";
            echo '            <div id="buttonContainer">'."\n";
            echo '                <input type="button" value="Zur&uuml;ck" onclick="history.back();"/>'."\n";
            echo '                <input type="button" value="Abbrechen" onclick="window.close();"/>'."\n";
            echo '                <input type="submit" value="Speichern" name="submit" onclick="document.getElementById(\'filename\').disabled=false;" />'."\n";
            echo "            </div>\n";
            echo "        </div>\n";
            echo "    </form>\n";
            echo "</body>\n";
            
        } else if ( empty( $_POST["htmlContent"] ) || empty( $_POST["filename"] ) ) {
            var_dump($_POST);
            echo "<body>\n";
            if ( empty( $_POST["htmlContent"] ) )  echo "    Fehler: Das Dokument war leer.<br />\n" ;
            if ( empty( $_POST["filename"] ) )     echo "    Fehler: Kein Dateiname angegeben.<br />\n";  
            echo '    <div id="buttonContainer">'."\n";
            echo "        <form>\n";
            echo '            <input type="button" value="Zur&uuml;ck" onclick="history.back();" />'."\n";
            echo '            <input type="button" value="Abbrechen" onclick="window.close();" />'."\n";
            echo "        </form>\n";
            echo "    </div>\n";
            echo "</body>\n";
        } else {
            if ( get_magic_quotes_gpc() )
                $postedValue = stripslashes( $_POST["htmlContent"] );
            else
                $postedValue = $_POST["htmlContent"];
            $result = file_put_contents( $html_basepath . "/" . $_POST["filename"], $postedValue);
            if ( !$result ) { //an error has occurred during saving
                echo "<body>\n";
                echo "    Fehler: Beim Speichern der Datei ist ein Fehler aufgetreten.<br />\n";
                echo "    Debug: Path: " . $html_basepath . "/" . $_POST["filename"] . "<br />\n";
                echo "    Result: " . (($result === false) ? 'false' : $result) . "<br />\n";
                echo "    Content: <pre>" . $_POST["htmlContent"] . "</pre>\n";
                echo '    <div id="buttonContainer">'."\n";
                echo "        <form>\n";
                echo '            <input type="button" value="Zur&uuml;ck" onclick="history.back();" />'."\n";
                echo '            <input type="button" value="Abbrechen" onclick="window.close();" />'."\n";
                echo "        </form>\n";
                echo "    </div>\n";
                echo "</body>\n";
            } else {
                echo "<body onload=\"opener.document.getElementById('URL" . $_GET["index"] . "').value='$basepath/components/com_content/files/html/" . $_POST["filename"] . "'\">\n";
                echo "    <fieldset><legend>Datei hochgeladen:</legend>\n";
                echo "        <div>Pfad auf dem Server (URL): $basepath/com_content/files/html/" . $_POST["filename"] . "</div>\n";
                echo '        <div style="display: none;">Pfad auf dem Server (Dateisystem): ' . $html_basepath . "/" . $_POST["filename"] . "</div>\n";
                echo "        <div>Dateigr&ouml;&szlig;e: $result Bytes</div>\n";
                echo "    </fieldset>\n";
                echo '    <div id="buttonContainer">'."\n";
                echo "        <form>\n";
                echo '            <input type="button" value="Schlie&szlig;en" onclick="window.close();" />'."\n";
                echo "        </form>\n";
                echo "    </div>\n";
                echo "</body>\n";
            }
        }
    } else {
        $_templates = scandir($template_basepath);
        unset ($template_files);
        foreach (preg_grep("/.xml$/i", $_templates) as $file) {
            if ( is_file($template_basepath . "/" . $file) && is_readable($template_basepath . "/" . $file) )
                $template_files[] = $template_basepath . "/" . $file;
        }
        unset ($_templates);
        unset ($templates);
        if ( isset($template_files) && is_array($template_files) ) {
            foreach ($template_files as $template_file) {
                unset ($template);
                if ( $xml = simplexml_load_file($template_file) ) {
                    if ( file_exists($template_basepath . "/" . $xml->htmlfile) ) {
                        $template["filename"] = basename($template_file);
                        foreach ($xml->children() as $key => $item)
                            $template[$key] = (string)$item;
                    }
                }
                if ( !empty($template) )
                    $templates[] = $template;
            }
        }
        
        if ( !empty( $_GET["oldfile"] ) ) {
            $filename = basename($_GET["oldfile"]);
        } else {
            if ( !file_exists($html_basepath . "/" . "newfile.html") ) {
                $filename = "newfile.html";
            } else {
                for ($i = 1; file_exists($html_basepath . "/" . ($filename = sprintf("newfile_%03d.html", $i)) ) ;$i++) ;
            }
        }
        
        echo "<body>\n";
        echo '    <script type="text/javascript">' . "\n";
        echo "    function check_values( obj ) {\n";
        echo "        sel = obj.options[obj.selectedIndex];\n";
        echo "        if (sel.value == 'divider' || sel.value == 'notemplates') {\n";
        echo "            document.getElementById('next').disabled=true;\n";
        echo "            document.getElementById('filename').disabled=true;\n";
        echo "        } else {\n";
        echo "            document.getElementById('next').disabled=false;\n";
        echo "            document.getElementById('filename').disabled=false;\n";
        echo "        }\n";
        echo "    }\n";
        echo "    </script>\n";
        echo '    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">' . "\n";
        echo "        <fieldset><legend>HTML-Editor - Vorlage auswählen</legend>\n";
        echo '            <select name="template_box" id="templatelist" size="10" style="width: 100%;" onclick="check_values( this );">'."\n";
        if ( !empty( $_GET["oldfile"] ) ) { 
        echo '                <option value="editold" selected="selected">Bestehende Datei bearbeiten</option>'."\n";
        echo '                <option value="divider">---------------</option>'."\n";
        }
        echo '                <option value="createnew"' . ( ( empty( $_GET["oldfile"] ) ) ? ' selected="selected"' : "" ) . '>Leere Datei erstellen</option>'."\n";
        echo '                <option value="divider">---------------</option>'."\n";
        if ( empty( $templates ) ) {
        echo '                <option value="notemplates">(Keine Vorlagen vorhanden)</option>'."\n";
        } else {
            foreach ($templates as $template) {
        echo '                <option value="' . $template["filename"] . '">' . $template["name"] . " -- " . $template["shortdesc"] . "</option>\n";
            }
        }
        echo "            </select>\n";
        echo "        </fieldset>\n";
        echo '        <div id="bottomline">'."\n";
        echo '            Dateiname: <input type="text" id="filename" name="filename" value="' . $filename . '" />'."\n";
        echo '            <div id="buttonContainer">'."\n";
        echo '                <input type="submit" value="Weiter" id="next" name="submit" />'."\n";
        echo '                <input type="button" value="Abbrechen" onclick="window.close();"/>'."\n";
        echo "            </div>\n";
        echo "        </div>\n";
        echo "    </form>\n";
        echo "</body>\n";
        
        
        
    } ?>
</html>