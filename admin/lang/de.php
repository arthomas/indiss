<?php
/**
 * @version     2010-01-09
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
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

    /*errors & general status messages:  err*, msg*  */
    $_LANG["errDBQryFailed"]                = "Die Datenbankanfrage ist fehlgeschlagen.";
    $_LANG["errGeneralParamError"]          = "Die &uuml;bergebenen Parameter enthielten einen Fehler.<br />Bitte <a href=\"index.php\">gehen Sie zur&uuml;ck zum Index</a>.";
    $_LANG["errCantLogout"]                 = "Fehler: Sie sind nicht angemeldet. Sie k&ouml;nnen sich nicht abmelden.";
    $_LANG["errComNotFound"]                = "Fehler: Die aufgerufene Komponente wurde nicht gefunden.";
    $_LANG["errDBError"]                    = "Datenbankfehler: %s";     //%s: The MySQL error message
    $_LANG["errDBErrorQry"]                 = "Query: <pre>%s</pre>";   //%s: The DB Query
    
    $_LANG["msgWrongPWorUN"]                = "Passwort oder Benutzername falsch.";
    $_LANG["msgLoginSuccess"]               = "Sie haben sich erfolgreich angemeldet.";
    $_LANG["msgLogoutSuccess"]              = "Sie haben sich erfolgreich abgemeldet.";
    
    
    /*General strings*/
    $_LANG["genNone"]                       = "Kein";
    $_LANG["genSave"]                       = "Speichern";
    $_LANG["genCancel"]                     = "Abbrechen";
    $_LANG["genLanguage"]                   = "Sprache";
    $_LANG["genLogin"]                      = "Anmelden";
    $_LANG["genLogout"]                     = "Abmelden";
    $_LANG["genYes"]                        = "Ja";
    $_LANG["genNo"]                         = "Nein";
    $_LANG["genClose"]                      = "Schlie&szlig;en";
    $_LANG["genBack"]                       = "Zur&uuml;ck";
    $_LANG["genForward"]                    = "Vorw&auml;rts";
    $_LANG["genDate"]                       = "Datum";
    $_LANG["genTime"]                       = "Zeit";
    $_LANG["genToday"]                      = "Heute";
    $_LANG["genTomorrow"]                   = "Morgen";
    $_LANG["genInTwoDays"]                  = "&Uuml;bermorgen";
    $_LANG["genCustomDate"]                 = "Anderes Datum";
    $_LANG["genCustomTime"]                 = "Anderer Zeitpunkt";
    $_LANG["genMorning"]                    = "Morgens";
    $_LANG["genMorningBreak"]               = "Gro&szlig;e Pause";
    $_LANG["genNoon"]                       = "Mittags";
    $_LANG["genEvening"]                    = "Abends";
    $_LANG["genResultingTimeStamp"]         = "Erzeugter Zeitstempel";
    
    
    /*Top nav items*/
    $_LANG["navContentManager"]             = "Inhaltsmanager";
    $_LANG["navContentFilesManager"]        = "Dateimanager";
    
    
    /*Ticker manager*/
    $_LANG["ticTickManHeadline"]            = "Tickermanager";
    
    $_LANG["ticNavList"]                    = "Liste der Ticker";
    $_LANG["ticNavCreate"]                  = "Neue Ticker erzeugen.";
    $_LANG["ticNavTrash"]                   = "Ticker Papierkorb";
    $_LANG["ticNavOptions"]                 = "Ticker Optionen";
    
    $_LANG["ticCreateSuccess"]              = "%d neue Ticker erfolgreich hinzugef&uuml;gt."; //%d: number of created tickers
    $_LANG["ticRestoreFromTrashSuccess"]    = "%d Ticker erfolgreich aus dem Papierkorb wiederhergestellt.";  //%d: The number of tickers restored
    $_LANG["ticMoveToTrashSuccess"]         = "%s Ticker erfolgreich in den Papierkorb verschoben."; //%s: number of moved tickers
    $_LANG["ticPermDeleteSuccess"]          = "%s Ticker erfolgreich gel&ouml;scht."; //%s: number of deleted tickers
    $_LANG["ticEditSaveSuccess"]            = "&Auml;nderungen an %d Ticker erfolgreich durchgef&uuml;hrt."; //%d: number of tickers edited
    
    $_LANG["ticExistingTickers"]            = "Vorhandene Ticker";
    
    $_LANG["ticCaption"]                    = "&Uuml;berschrift";
    $_LANG["ticContent"]                    = "Inhalt";
    $_LANG["ticDispFrom"]                   = "Angezeigt von";
    $_LANG["ticDispUntil"]                  = "Angezeigt bis";
    
    $_LANG["ticPastTickers"]                = "Fr&uuml;her angezeigte Ticker";
    $_LANG["ticPresentTickers"]             = "Gegenw&auml;rtig aktive Ticker";
    $_LANG["ticFutureTickers"]              = "Zuk&uuml;nftig anzuzeigende Ticker";
    
    $_LANG["ticEdit"]                       = "Bearbeiten";
    $_LANG["ticEditShort"]                  = "B";
    $_LANG["ticEditSelected"]               = "Auswahl bearbeiten";
    $_LANG["ticDelete"]                     = "L&ouml;schen";
    $_LANG["ticDeleteShort"]                = "L";
    $_LANG["ticDeleteSelected"]             = "Auswahl l&ouml;schen";
    $_LANG["ticDelete2"]                    = "Endg&uuml;ltig l&ouml;schen";
    $_LANG["ticDelete2Short"]               = "L";
    $_LANG["ticDelete2Selected"]            = "Auswahl endg&uuml;ltig l&ouml;schen";
    $_LANG["ticRestor"]                     = "Wiederherstellen";
    $_LANG["ticRestoreShort"]               = "W";
    $_LANG["ticRestoreSelected"]            = "Auswahl wiederherstellen";
    $_LANG["ticSelectMultiple"]             = "Mehrfachauswahl";
    
    $_LANG["ticCreateTicker"]               = "Ticker erzeugen";
    $_LANG["ticReloadCreate1"]              = "Neu laden und";  //A drop-down menu to select the number will be displayed between these two strings.
    $_LANG["ticReloadCreate2"]              = "Felder f&uuml; neue Ticker erzeugen.";       //no need to include spaces at the edges
    $_LANG["ticEmptyCapConNotice"]          = "Ticker ohne Inhalt <i>und</i> &Uuml;berschrift werden ignoriert (Eines der beiden Felder darf leer sein).";
    
    $_LANG["ticYesReallyDelete"]            = "Ja, wirklich l&ouml;schen";
    $_LANG["ticNoDontDelete"]               = "Nein, nicht l&ouml;schen";
    $_LANG["ticMoveToTrash?"]               = "Wollen Sie wirklich alle aufgelisteten Ticker in den Papierkorb verschieben? (Sie k&ouml;nnen sie sp&auml;ter wiederherstellen.)";
    $_LANG["ticDeletePermanently?"]         = "Wollen Sie wirklich alle aufgelisteten Ticker endg&uuml;ltig l&ouml;schen? (Dies kann nicht r&uuml;ckg&auml;ngig gemacht werden!)";
    $_LANG["ticTickersToDelete"]            = "Ticker, die gel&ouml;scht werden sollen";
    
    $_LANG["ticTrashBin"]                   = "Ticker Papierkorb";
    
    $_LANG["ticEditTicker"]                 = "Ticker bearbeiten";
    $_LANG["ticDeleted"]                    = "Gel&ouml;scht";
    $_LANG["ticDeletedInfo"]                = "This ticker is currently in the trash bin. If you want to restore it from there when you click save, un-check this box. Otherwise, leave it checked.";
    
    
    /*HTML pages manager*/
    $_LANG["conPageManHeadline"]            = "Inhaltsmanager";
    
    $_LANG["conNavList"]                    = "Inhaltsliste";
    $_LANG["conNavCreate"]                  = "Neuen Inhalt hinzuf&uuml;gen";
    $_LANG["conNavTrash"]                   = "Inhaltspapierkorb";
    $_LANG["conNavOptions"]                 = "Inhaltsoptionen";
    
    $_LANG["conCreateSuccess"]              = "Successfully added %d content items."; //%d: number of created content items
    $_LANG["conRestoreFromTrashSuccess"]    = "Successfully restored %d content items from the trash bin.";  //%d: The number of content items restored
    $_LANG["conMoveToTrashSuccess"]         = "Successfully moved %s content items to the trash bin."; //%s: number of moved content items
    $_LANG["conPermDeleteSuccess"]          = "Successfully deleted %s content items."; //%s: number of deleted content items
    $_LANG["conEditSaveSuccess"]            = "Successfully saved edits to %d content items."; //%d: number of content items edited
    
    $_LANG["conExistingPages"]              = "Existing content items";
    
    $_LANG["conName"]                       = "Name";
    $_LANG["conURL"]                        = "URL";
    $_LANG["conTags"]                       = "Tags";
    $_LANG["conDispTime"]                   = "T";
    $_LANG["conDispFrom"]                   = "Angezeigt von";
    $_LANG["conDispUntil"]                  = "Angezeigt bis";
    $_LANG["conType"]                       = "Typ";
    
    $_LANG["conTypeLocalPage"]              = "Lokale Seite";
    $_LANG["conTypeExternalPage"]           = "Externe Seite";
    $_LANG["conTypeLocalImage"]             = "Lokale Bilddatei";
    $_LANG["conTypeExternalImage"]          = "Externe Bilddatei";
    $_LANG["conTypeLocalPDF"]               = "Lokale PDF-Datei";
    $_LANG["conTypeExternalPDF"]            = "Externe PDF-Datei";
    $_LANG["conTypeLocalFlash"]             = "Lokale Flash-Datei";
    $_LANG["conTypeExternalFlash"]          = "Externe Flash-Datei";
    $_LANG["conTypeLocalOther"]             = "Andere lokale Datei";
    $_LANG["conTypeExternalOther"]          = "Andere externe Datei";
    $_LANG["conTypeUnknown"]                = "Unbekannt";
    
    $_LANG["conPastPages"]                  = "Fr&uuml;her angezeigte Eintr&auml;ge";
    $_LANG["conPresentPages"]               = "Gegenw&auml;rtig aktive Eintr&auml;ge";
    $_LANG["conFuturePages"]                = "Zuk&uuml;nftig anzuzeigende Eintr&auml;ge";
    
    $_LANG["conEdit"]                       = "Bearbeiten";
    $_LANG["conEditShort"]                  = "B";
    $_LANG["conEditSelected"]               = "Auswahl bearbeiten";
    $_LANG["conDelete"]                     = "L&ouml;schen";
    $_LANG["conDeleteShort"]                = "L";
    $_LANG["conDeleteSelected"]             = "Auswahl l&ouml;schen";
    $_LANG["conDelete2"]                    = "Endg&uuml;ltig l&ouml;schen";
    $_LANG["conDelete2Short"]               = "L";
    $_LANG["conDelete2Selected"]            = "Auswahl endg&uuml;ltig l&ouml;schen";
    $_LANG["conRestor"]                     = "Wiederherstellen";
    $_LANG["conRestoreShort"]               = "W";
    $_LANG["conRestoreSelected"]            = "Auswahl wiederherstellen";
    $_LANG["conSelectMultiple"]             = "Mehrfachauswahl";
    
    $_LANG["conCreateItem"]                 = "Neuer Inhalt";
    $_LANG["conReloadCreate1"]              = "Mit";        //A drop-down menu to select the number will be displayed between these two strings.
    $_LANG["conReloadCreate2"]              = "Eintr&auml;gen neu laden.";         //no need to include spaces at the edges
    $_LANG["conEmptyURLNotice"]             = "Eintr&auml;ge mit leerer URL werden ignoriert (Namen sind optional).";
    
    $_LANG["conYesReallyDelete"]            = "Ja, wirklich l&ouml;schen";
    $_LANG["conNoDontDelete"]               = "Nein, nicht l&ouml;schen";
    $_LANG["conMoveToTrash?"]               = "Do you really want to move all pages listed below to the trash bin? (You can later restore them while they are still in the trash bin)";
    $_LANG["conDeletePermanently?"]         = "Do you really want to delete all pages listed below permanently? (This cannot be undone!)";
    $_LANG["conPagesToDelete"]              = "Elemente, die gel&ouml;scht werden sollen";
    
    $_LANG["conTrashBin"]                   = "Gel&ouml;schte Eintr&auml;ge - Papierkorb";
    
    $_LANG["conEditItem"]                   = "Inhalt bearbeiten";
    $_LANG["conDeleted"]                    = "Gel&ouml;scht";
    $_LANG["conDeletedInfo"]                = "Dieses Element befindet sich im Moment im Papierkorb. Deaktivieren Sie diese Box, um das Element beim Speichern wiederherzustellen. Andernfalls belassen Sie sie aktiv.";
    $_LANG["conEnabled"]                    = "Aktiviert";
    
    $_LANG["conBrowseServer"]               = "Server durchsuchen...";
    $_LANG["conUploadFile"]                 = "Datei hochladen...";
    $_LANG["conCreateFile"]                 = "Datei erstellen...";
    $_LANG["conEditFile"]                   = "Datei bearbeiten...";
    $_LANG["conOpenEditor"]                 = "Editor &ouml;ffnen...";
    
    $_LANG["conIgnore1"]                    = "Dieser Eintrag wird ignoriert: "; //ATTENTION! this block will be inserted into javascript. do not escape any entities (html chars, umlauts, etc). however, escape PHP special chars twice (once for PHP, once for JS)!!
    $_LANG["conIgnoreEmptyURL"]             = "Leere URL.";
    $_LANG["conIgnoreUnsuppProt"]           = "Protkoll nicht unterstützt.";
    $_LANG["conIgnoreDispTime"]             = "Ungültiger Werte im Feld \\\"Angezeigt für\\\".";
    $_LANG["conThisIsLocalPage"]            = "This is a local page.";
    $_LANG["conThisIsExternalPage"]         = "This is an external page.";
    $_LANG["conThisIsLocalImage"]           = "This is a local image.";
    $_LANG["conThisIsExternalImage"]        = "This is an external image.";
    $_LANG["conThisIsLocalPDF"]             = "This is a local PDF file.";
    $_LANG["conThisIsExternalPDF"]          = "This is an external PDF file.";
    $_LANG["conThisIsLocalFlash"]           = "This is a local flash file.";
    $_LANG["conThisIsExternalFlash"]        = "This is an external flash file.";
    $_LANG["conThisIsLocalOther"]           = "This is a local link (type not recognized).";
    $_LANG["conThisIsExternalOther"]        = "This is an external link (type not recognized).";

?>