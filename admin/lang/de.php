<?php
/**
 * @version     2009-09-19
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
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
    $_LANG["errDBQryFailed"]                = "The database query failed.";
    $_LANG["errGeneralParamError"]          = "There was an error in the parameters passed on.<br />Please <a href=\"index.php\">go back to the index</a>.";
    $_LANG["errCantLogout"]                 = "Error: You are not logged in. You cannot log out.";
    $_LANG["errComNotFound"]                = "Error: The component you tried to open was not found.";
    $_LANG["errDBError"]                    = "Database error: %s";     //%s: The MySQL error message
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
    $_LANG["ticPresentTickers"]             = "Gegenw&auml;rtig angezeigte Ticker";
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
    $_LANG["ticMoveToTrash?"]               = "Wollen Sie wirklich alle aufgelisteten Ticker in den Papierkorb verschieben? (Sie können sie sp&auml;ter wiederherstellen.)";
    $_LANG["ticDeletePermanently?"]         = "Wollen Sie wirklich alle aufgelisteten Ticker endg&uuml;ltig l&ouml;schen? (Dies kann nicht r&uuml;ckg&auml;ngig gemacht werden!)";
    $_LANG["ticTickersToDelete"]            = "Ticker, die gel&ouml;scht werden sollen";
    
    $_LANG["ticTrashBin"]                   = "Papierkorb";
    
    $_LANG["ticEditTicker"]                 = "Ticker bearbeiten";
    $_LANG["ticDeleted"]                    = "Gel&ouml;scht";
    $_LANG["ticDeletedInfo"]                = "This ticker is currently in the trash bin. If you want to restore it from there when you click save, un-check this box. Otherwise, leave it checked.";
    
    
    /*HTML pages manager*/
    $_LANG["conPageManHeadline"]            = "Content manager";
    
    $_LANG["conNavList"]                    = "List of content";
    $_LANG["conNavCreate"]                  = "Add new content";
    $_LANG["conNavTrash"]                   = "Content trash bin";
    $_LANG["conNavOptions"]                 = "Content options";
    
    $_LANG["conCreateSuccess"]              = "Successfully added %d content items."; //%d: number of created content items
    $_LANG["conRestoreFromTrashSuccess"]    = "Successfully restored %d content items from the trash bin.";  //%d: The number of content items restored
    $_LANG["conMoveToTrashSuccess"]         = "Successfully moved %s content items to the trash bin."; //%s: number of moved content items
    $_LANG["conPermDeleteSuccess"]          = "Successfully deleted %s content items."; //%s: number of deleted content items
    $_LANG["conEditSaveSuccess"]            = "Successfully saved edits to %d content items."; //%d: number of content items edited
    
    $_LANG["conExistingPages"]              = "Existing content items";
    
    $_LANG["conName"]                       = "Name";
    $_LANG["conURL"]                        = "URL";
    $_LANG["conDispTime"]                   = "Displayed for";
    $_LANG["conDispFrom"]                   = "Displayed from";
    $_LANG["conDispUntil"]                  = "Displayed until";
    $_LANG["conType"]                       = "Type";
    
    $_LANG["conTypeLocalPage"]              = "Lokale Seite";
    $_LANG["conTypeExternalPage"]           = "Externe Seite";
    $_LANG["conTypeLocalImage"]             = "Lokale Bilddatei";
    $_LANG["conTypeExternalImage"]          = "Externe Bilddatei";
    $_LANG["conTypeLocalOther"]             = "Andere lokale Datei";
    $_LANG["conTypeExternalOther"]          = "Andere externe Datei";
    $_LANG["conTypeUnknown"]                = "Unbekannt";
    
    $_LANG["conPastPages"]                  = "Previous pages";
    $_LANG["conPresentPages"]               = "Current pages";
    $_LANG["conFuturePages"]                = "Future pages";
    
    $_LANG["conEdit"]                       = "Edit";
    $_LANG["conEditShort"]                  = "E";
    $_LANG["conEditSelected"]               = "Edit selected";
    $_LANG["conDelete"]                     = "Delete";
    $_LANG["conDeleteShort"]                = "D";
    $_LANG["conDeleteSelected"]             = "Delete selected";
    $_LANG["conDelete2"]                    = "Delete permanently";
    $_LANG["conDelete2Short"]               = "D";
    $_LANG["conDelete2Selected"]            = "Delete selected permanently";
    $_LANG["conRestor"]                     = "Restore";
    $_LANG["conRestoreShort"]               = "R";
    $_LANG["conRestoreSelected"]            = "Restore selected";
    $_LANG["conSelectMultiple"]             = "Select multiple";
    
    $_LANG["conCreatePage"]                 = "Neuer Inhalt";
    $_LANG["conReloadCreate1"]              = "Mit";        //A drop-down menu to select the number will be displayed between these two strings.
    $_LANG["conReloadCreate2"]              = "Eintr&auml;gen neu laden.";         //no need to include spaces at the edges
    $_LANG["conEmptyURLNotice"]             = "Eintr&auml;ge mit leerer URL werden ignoriert (Namen sind optional).";
    
    $_LANG["conYesReallyDelete"]            = "Ja, wirklich l&ouml;schen";
    $_LANG["conNoDontDelete"]               = "Nein, nicht l&ouml;schen";
    $_LANG["conMoveToTrash?"]               = "Do you really want to move all pages listed below to the trash bin? (You can later restore them while they are still in the trash bin)";
    $_LANG["conDeletePermanently?"]         = "Do you really want to delete all pages listed below permanently? (This cannot be undone!)";
    $_LANG["conPagesToDelete"]              = "Pages to be deleted";
    
    $_LANG["conTrashBin"]                   = "Gel&ouml;schte Eintr&auml;ge - Papierkorb";
    
    $_LANG["conEditPage"]                   = "Edit page";
    $_LANG["conDeleted"]                    = "Deleted";
    $_LANG["conDeletedInfo"]                = "This page is currently in the trash bin. If you want to restore it from there when you click save, un-check this box. Otherwise, leave it checked.";

?>