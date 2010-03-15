<?php
/**
 * @version     2009-09-26
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
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
    
    $_LANG["msgWrongPWorUN"]                = "Sorry, wrong password or username.";
    $_LANG["msgLoginSuccess"]               = "You have successfully logged in.";
    $_LANG["msgLogoutSuccess"]              = "You have successfully logged out.";
    
    
    /*General strings*/
    $_LANG["genNone"]                       = "None";
    $_LANG["genSave"]                       = "Save";
    $_LANG["genCancel"]                     = "Cancel";
    $_LANG["genLanguage"]                   = "Language";
    $_LANG["genLogin"]                      = "Log in";
    $_LANG["genLogout"]                     = "Log out";
    $_LANG["genYes"]                        = "Yes";
    $_LANG["genNo"]                         = "No";
    $_LANG["genClose"]                      = "Close";
    $_LANG["genBack"]                       = "Back";
    $_LANG["genForward"]                    = "Forward";
    $_LANG["genDate"]                       = "Date";
    $_LANG["genTime"]                       = "Time";
    $_LANG["genToday"]                      = "Today";
    $_LANG["genTomorrow"]                   = "Tomorrow";
    $_LANG["genInTwoDays"]                  = "In two days";
    $_LANG["genCustomDate"]                 = "Custom date";
    $_LANG["genCustomTime"]                 = "Custom time";
    $_LANG["genMorning"]                    = "Morning";
    $_LANG["genMorningBreak"]               = "Morning break";
    $_LANG["genNoon"]                       = "Noon";
    $_LANG["genEvening"]                    = "Evening";
    $_LANG["genResultingTimeStamp"]         = "Resulting time stamp";
    
    
    /*Top nav items*/
    $_LANG["navContentManager"]             = "Content manager";
    $_LANG["navContentFilesManager"]        = "Content files manager";
    
    
    /*Ticker manager*/
    $_LANG["ticTickManHeadline"]            = "Ticker manager";
    
    $_LANG["ticNavList"]                    = "List of tickers";
    $_LANG["ticNavCreate"]                  = "Create new ticker(s)";
    $_LANG["ticNavTrash"]                   = "Ticker trash bin";
    $_LANG["ticNavOptions"]                 = "Ticker options";
    
    $_LANG["ticCreateSuccess"]              = "Successfully added %d tickers."; //%d: number of created tickers
    $_LANG["ticRestoreFromTrashSuccess"]    = "Successfully restored %d tickers from the trash bin.";  //%d: The number of tickers restored
    $_LANG["ticMoveToTrashSuccess"]         = "Successfully moved %s tickers to the trash bin."; //%s: number of moved tickers
    $_LANG["ticPermDeleteSuccess"]          = "Successfully deleted %s tickers."; //%s: number of deleted tickers
    $_LANG["ticEditSaveSuccess"]            = "Successfully saved edits to %d tickers."; //%d: number of tickers edited
    
    $_LANG["ticExistingTickers"]            = "Existing tickers";
    
    $_LANG["ticCaption"]                    = "Caption";
    $_LANG["ticContent"]                    = "Content";
    $_LANG["ticDispFrom"]                   = "Displayed from";
    $_LANG["ticDispUntil"]                  = "Displayed until";
    
    $_LANG["ticPastTickers"]                = "Previous tickers";
    $_LANG["ticPresentTickers"]             = "Current tickers";
    $_LANG["ticFutureTickers"]              = "Future tickers";
    
    $_LANG["ticEdit"]                       = "Edit";
    $_LANG["ticEditShort"]                  = "E";
    $_LANG["ticEditSelected"]               = "Edit selected";
    $_LANG["ticDelete"]                     = "Delete";
    $_LANG["ticDeleteShort"]                = "D";
    $_LANG["ticDeleteSelected"]             = "Delete selected";
    $_LANG["ticDelete2"]                    = "Delete permanently";
    $_LANG["ticDelete2Short"]               = "D";
    $_LANG["ticDelete2Selected"]            = "Delete selected permanently";
    $_LANG["ticRestor"]                     = "Restore";
    $_LANG["ticRestoreShort"]               = "R";
    $_LANG["ticRestoreSelected"]            = "Restore selected";
    $_LANG["ticSelectMultiple"]             = "Select multiple";
    
    $_LANG["ticCreateTicker"]               = "Create ticker";
    $_LANG["ticReloadCreate1"]              = "Reload and create";  //A drop-down menu to select the number will be displayed between these two strings.
    $_LANG["ticReloadCreate2"]              = "new tickers.";       //no need to include spaces at the edges
    $_LANG["ticEmptyCapConNotice"]          = "Tickers with both content <i>and</i> caption will be ignored (you can create tickers with one of these fields empty).";
    
    $_LANG["ticYesReallyDelete"]            = "Yes, really delete";
    $_LANG["ticNoDontDelete"]               = "No, don't delete";
    $_LANG["ticMoveToTrash?"]               = "Do you really want to move all tickers listed below to the trash bin? (You can later restore them while they are still in the trash bin)";
    $_LANG["ticDeletePermanently?"]         = "Do you really want to delete all tickers listed below permanently? (This cannot be undone!)";
    $_LANG["ticTickersToDelete"]            = "Tickers to be deleted";
    
    $_LANG["ticTrashBin"]                   = "Deleted tickers - Trash bin";
    
    $_LANG["ticEditTicker"]                 = "Edit ticker";
    $_LANG["ticDeleted"]                    = "Deleted";
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
    
    $_LANG["conTypeLocalPage"]              = "Local page";
    $_LANG["conTypeExternalPage"]           = "External page";
    $_LANG["conTypeLocalImage"]             = "Local image";
    $_LANG["conTypeExternalImage"]          = "External image";
    $_LANG["conTypeLocalPDF"]               = "Local PDF file";
    $_LANG["conTypeExternalPDF"]            = "External PDF file";
    $_LANG["conTypeLocalFlash"]             = "Local flash file";
    $_LANG["conTypeExternalFlash"]          = "External flash file";
    $_LANG["conTypeLocalOther"]             = "Other local file";
    $_LANG["conTypeExternalOther"]          = "Other external file";
    $_LANG["conTypeUnknown"]                = "Unknown";
    
    $_LANG["conPastPages"]                  = "Previous content items";
    $_LANG["conPresentPages"]               = "Current content items";
    $_LANG["conFuturePages"]                = "Future content items";
    
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
    
    $_LANG["conCreateItem"]                 = "Add new content";
    $_LANG["conReloadCreate1"]              = "Reload with";        //A drop-down menu to select the number will be displayed between these two strings.
    $_LANG["conReloadCreate2"]              = "new content items.";         //no need to include spaces at the edges
    $_LANG["conEmptyURLNotice"]             = "Items with empty URL will be ignored (names are optional).";
    
    $_LANG["conYesReallyDelete"]            = "Yes, really delete";
    $_LANG["conNoDontDelete"]               = "No, don't delete";
    $_LANG["conMoveToTrash?"]               = "Do you really want to move all items listed below to the trash bin? (You can later restore them while they are still in the trash bin)";
    $_LANG["conDeletePermanently?"]         = "Do you really want to delete all items listed below permanently? (This cannot be undone!)";
    $_LANG["conPagesToDelete"]              = "Items to be deleted";
    
    $_LANG["conTrashBin"]                   = "Deleted content items - Trash bin";
    
    $_LANG["conEditItem"]                   = "Edit item";
    $_LANG["conDeleted"]                    = "Deleted";
    $_LANG["conDeletedInfo"]                = "This item is currently in the trash bin. If you want to restore it from there when you click save, un-check this box. Otherwise, leave it checked.";
    $_LANG["conEnabled"]                    = "Enabled";
    
    $_LANG["conBrowseServer"]               = "Browse server...";
    $_LANG["conUploadFile"]                 = "Upload file...";
    $_LANG["conCreateFile"]                 = "Create file...";
    $_LANG["conEditFile"]                   = "Edit file...";
    $_LANG["conOpenEditor"]                 = "Open Editor...";
    
    $_LANG["conIgnore1"]                    = "This entry will be ignored: "; //ATTENTION! this block will be inserted into javascript. do not escape any entities (html chars, umlauts, etc). however, escape PHP special chars twice (once for PHP, once for JS)!!
    $_LANG["conIgnoreEmptyURL"]             = "Empty URL.";
    $_LANG["conIgnoreUnsuppProt"]           = "Unsupported protocol.";
    $_LANG["conIgnoreDispTime"]             = "Invalid display time.";
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