<?php
/**
 * @version     2009-12-03
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      Installation script - creates necessary database tables and default entries
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

//TODO: install: create installation script

    define("__INSTALL", 1);
    
    $lang = $defaultlang = "de";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="author" content="Patrick Lehner" />

    <link rel="stylesheet" type="text/css" href="installation.css" />
    
    <title>INDISS Installation -- SPEZIALVERSION 0.1.0a</title>
    
    <script type="text/javascript" language="Javascript">
    function checkThis() {
        var r = true;
        if (document.getElementById('dbhost').value == "") {
            document.getElementById('dbhostrow').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbhostnote').firstChild.nodeValue = "Dieses Feld darf nicht leer bleiben";
            r = false;
        } else {
            document.getElementById('dbhostrow').style.backgroundColor = "transparent";
            document.getElementById('dbhostnote').firstChild.nodeValue = "";
        }
        if (document.getElementById('dbname').value == "") {
            document.getElementById('dbnamerow').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbnamenote').firstChild.nodeValue = "Dieses Feld darf nicht leer bleiben";
            r = false;
        } else {
            document.getElementById('dbnamerow').style.backgroundColor = "transparent";
            document.getElementById('dbnamenote').firstChild.nodeValue = "";
        }
        if (document.getElementById('dbuser').value == "") {
            document.getElementById('dbuserrow').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbusernote').firstChild.nodeValue = "Dieses Feld darf nicht leer bleiben";
            r = false;
        } else {
            document.getElementById('dbuserrow').style.backgroundColor = "transparent";
            document.getElementById('dbusernote').firstChild.nodeValue = "";
        }
        if (document.getElementById('dbpass1').value != document.getElementById('dbpass2').value) {
            document.getElementById('dbpass1row').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbpass2row').style.backgroundColor = "#FFBBBB";
            document.getElementById('dbpassnote').firstChild.nodeValue = "Passwörter stimmen nicht überein";
            r = false;
        } else {
            document.getElementById('dbpass1row').style.backgroundColor = "transparent";
            document.getElementById('dbpass2row').style.backgroundColor = "transparent";
            document.getElementById('dbpassnote').firstChild.nodeValue = "";
        }

        if (document.getElementById('adminpw1').value == "") {
            document.getElementById('adminpw1row').style.backgroundColor = "#FFBBBB";
            document.getElementById('adminpw1note').firstChild.nodeValue = "Dieses Feld darf nicht leer bleiben";
            document.getElementById('adminpw2note').firstChild.nodeValue = " ";
            r = false;
        } else if (document.getElementById('adminpw1').value != document.getElementById('adminpw2').value) {
            document.getElementById('adminpw1row').style.backgroundColor = "#FFBBBB";
            document.getElementById('adminpw2row').style.backgroundColor = "#FFBBBB";
            document.getElementById('adminpw1note').firstChild.nodeValue = " ";
            document.getElementById('adminpw2note').firstChild.nodeValue = "Passwörter stimmen nicht überein";
            r = false;
        } else {
            document.getElementById('adminpw1row').style.backgroundColor = "transparent";
            document.getElementById('adminpw2row').style.backgroundColor = "transparent";
            document.getElementById('adminpw1note').firstChild.nodeValue = " ";
            document.getElementById('adminpw2note').firstChild.nodeValue = " ";
        }
        
        if ( document.getElementById('screenresx').value == "" || document.getElementById('screenresy').value == "" ) {
            document.getElementById('screenresrow').style.backgroundColor = "#FFBBBB";
            document.getElementById('screenresnote').firstChild.nodeValue = "Dieses Feld darf nicht leer bleiben";
            r = false;
        } else if ( String(document.getElementById('screenresx').value).search(/[^\d]/i) != -1 || String(document.getElementById('screenresy').value).search(/[^\d]/i) != -1 ) {
            document.getElementById('screenresrow').style.backgroundColor = "#FFBBBB";
            document.getElementById('screenresnote').firstChild.nodeValue = "Dieses Feld darf nur Zahlen enthalten";
            r = false;
        } else {
            document.getElementById('screenresrow').style.backgroundColor = "transparent";
            document.getElementById('screenresnote').firstChild.nodeValue = " ";
        }

        return r;
    }
    </script>
</head>
<body>
<?php if (!isset($_POST["submitted"])) {?>
    <fieldset id="container"><legend>Ben&ouml;tigte Daten</legend>
        <form method="post" action="index.php">
            <div class="enterdata">
                Geben Sie hier die Zugangsdaten für die Datenbank ein:
                <table summary="" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td>Datenbank-Typ:</td>
                            <td>MySQL<input type="hidden" name="dbtype" value="MySQL" /></td>
                        </tr>
                        <tr id="dbhostrow">
                            <td>Datenbank-Host:</td>
                            <td><input type="text" name="dbhost" id="dbhost" value="localhost" /> <span id="dbhostnote" style="color: red;" class="small">&nbsp;</span><div class="small">Dies ist vermutlich 'localhost'.</div></td>
                        </tr>
                        <tr id="dbnamerow">
                            <td>Datenbank-Name:</td>
                            <td><input type="text" name="dbname" id="dbname" value="indiss" /> <span id="dbnamenote" style="color: red;" class="small">&nbsp;</span></td>
                        </tr>
                        <tr id="dbuserrow">
                            <td>Datenbank-Benutzername:</td>
                            <td><input type="text" name="dbuser" id="dbuser" /> <span id="dbusernote" style="color: red;" class="small">&nbsp;</span></td>
                        </tr>
                        <tr id="dbpass1row">
                            <td style="padding-bottom: 0;">Datenbank-Passwort:</td>
                            <td style="padding-bottom: 0;"><input type="password" name="dbpass1" id="dbpass1" /> <span id="dbpassnote" style="color: red;" class="small">&nbsp;</span></td>
                        </tr>
                        <tr id="dbpass2row">
                            <td style="padding-top: 2px;">Passwort best&auml;tigen:</td>
                            <td style="padding-top: 2px;"><input type="password" name="dbpass2" id="dbpass2" /></td>
                        </tr>
                    </tbody>
                </table>
                <hr />
                <table summary="" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="4"><h3 style="display:inline;">Admin-Passwort</h3> Geben Sie ein Passwort für das Administrator-Konto ein</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">&nbsp;</td>
                            <td style="width: 20%;">Benutzername:</td>
                            <td style="width: 40%;">admin</td>
                            <td style="width: 20%;">&nbsp;</td>
                        </tr>
                        <tr id="adminpw1row">
                            <td style="width: 20%;">&nbsp;</td>
                            <td style="width: 20%;">Passwort:</td>
                            <td style="width: 40%;"><input type="password" name="adminpw1" id="adminpw1" maxlength="100" /> <span id="adminpw1note" style="color: red;" class="small">&nbsp;</span></td>
                            <td style="width: 20%;">&nbsp;</td>
                        </tr>
                        <tr id="adminpw2row">
                            <td style="width: 20%;">&nbsp;</td>
                            <td style="width: 20%;">Passwort wiederholen:</td>
                            <td style="width: 40%;"><input type="password" name="adminpw2" id="adminpw2" maxlength="100" /> <span id="adminpw2note" style="color: red;" class="small">&nbsp;</span></td>
                            <td style="width: 20%;">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                <hr />
                <table summary="" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="3"><h3 style="display:inline;">Bildschirmaufl&ouml;sung</h3> Geben Sie die Aufl&ouml;sung des Bildschirms ein, auf dem INDISS angezeigt wird</td>
                        </tr>
                        <tr id="screenresrow">
                            <td style="width: 20%;">&nbsp;</td>
                            <td style="width: 60%; text-align: center;">Aufl&ouml;sung: <input type="text" name="screenresx" id="screenresx" maxlength="6" size="5" value="1920" /> x <input type="text" name="screenresy" id="screenresy" maxlength="6" size="5" value="1080" /> Pixel (<i>Breite</i> x <i>H&ouml;he</i>)<br /><span id="screenresnote" style="color: red;" class="small">&nbsp;</span><span class="small">&nbsp;</span></td>
                            <td style="width: 20%;">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <table id="buttonbar" summary="" cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <tr>
                            <td id="leftbox"></td>
                            <td id="rightbox"><input type="hidden" name="submitted" value="yes" /><input type="button" name="next" value="Installieren!" id="nextbutton" onclick="if (checkThis()) {this.form.submit();}" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </fieldset>
<?php } else { 
    
    // Open connection to MySQL server
    if(!mysql_connect($_POST["dbhost"], $_POST["dbuser"], $_POST["dbpass1"])) { 
        die("Datenbankverbindung fehlgeschlagen: " . mysql_error());
    }
    
    $result = mysql_query( "CREATE DATABASE IF NOT EXISTS `" . $_POST["dbname"] . "`" );
    if ( !$result ) {
        die("Erstellen der Datenbank fehlgeschlagen. Datenbank-Fehler:" . mysql_error());
    }
    
    if(!mysql_select_db($_POST["dbname"])) { 
        die("Ausw&auml;hlen der Datenbank fehlgeschlagen. Datenbank-Fehler:" . mysql_error());
    }
    
    $configFile = file_get_contents("config.php-dist");
    if (!$configFile) {
        die("Konnte Vorlage f&uuml;r config-Datei nicht &ouml;ffnen");
    }
    
    if (!($queryList = explode(";", file_get_contents("indiss_branch.sql")))) {
        die("Konnte die SQL-Struktur-Vorlage nicht &ouml;ffnen");
    }
    
    $queryList[] = "INSERT INTO `users` (`username`, `password`, `email`, `type`) VALUES ('admin', '" . sha1($_POST["adminpw1"]) . "', '', 'admin')";
    $queryList[] = "UPDATE `global_view_options` SET `value` = '". $_POST["screenresx"] ."' WHERE `name` = 'screenDimensionX' LIMIT 1";
    $queryList[] = "UPDATE `global_view_options` SET `value` = '". $_POST["screenresy"] ."' WHERE `name` = 'screenDimensionY' LIMIT 1";
    
    foreach ($queryList as $query) {
        $query = trim($query);
        if (!empty($query))
            if (!($result = mysql_query($query))) {
                die("Erzeugen der SQL-Tabellen fehlgeschlagen. Datenbank-Fehler: " . mysql_error() . "; Query: <pre>$query</pre>");
            }
    }
    
    
    $configFile = str_replace("%defaultlang%", $lang, $configFile);
    $configFile = str_replace("%dbhost%", $_POST["dbhost"], $configFile);
    $configFile = str_replace("%dbuser%", $_POST["dbuser"], $configFile);
    $configFile = str_replace("%dbpass%", $_POST["dbpass1"], $configFile);
    $configFile = str_replace("%dbname%", $_POST["dbname"], $configFile);
    
    if (!file_put_contents("../config/config.php", $configFile)) {
        die("Schreiben der config-Datei fehlgeschlagen");
    }
    
    ?>
    Die Installation war erfolgreich.<br />
    L&ouml;schen Sie nun den Ordner <span style="font-style: italic; font-family: monospace;">installation</span> samt seines Inhaltes.
<?php } ?>
</body>
</html>