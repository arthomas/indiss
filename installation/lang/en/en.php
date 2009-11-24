<?php
/**
 * @version     2009-11-11
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      English language file for installation
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
 
    $_LANG["genNext"]                       = "Next";  //Note: for Next and Back buttons, don't include the arrows. They will be added by the displaying script.
    $_LANG["genBack"]                       = "Back";

    $_LANG["1PageTitle"]                    = "InfoScreen Installation - Step 1: Language selection";
    $_LANG["1WelcomeMsg"]                   = "Welcome to the InfoScreen installation script!";
    $_LANG["1PlsSelectLang"]                = "Please select the language for this installation:";
    $_LANG["1UseAsDefaultLang"]             = "Use this language as default language for InfoScreen as well";
    
    $_LANG["2PageTitle"]                    = "InfoScreen Installation - Step 2: Introduction and license";
    $_LANG["2ThisWizard"]                   = "This wizard will guide you through the installation of InfoScreen. It will ask you to enter some default settings and an administrator password. Once you have completed entering all necessary data, the script will create the required database tables and save all settings.";
    $_LANG["2PleaseNote"]                   = "Please note: This script will change neither the database nor the file system until you confirm all settings in the final installation step.";
    $_LANG["2LicenseDisplay"]               = "In the following box, a full copy of the GNU General Public License v3 will be displayed. By installing and using InfoScreen, you agree to this license, even if you by some means skip this step or the whole installation script.";
    $_LANG["2OrigDocAt"]                    = "The original document can be viewed at";
    
    $_LANG["3PageTitle"]                    = "InfoScreen Installation - Step 3: Database setup";
    $_LANG["3PleaseEnterData"]              = "Please enter the data required to access the database below.";
    $_LANG["3TablePermission"]              = "The used database must either already contain a table named 'infoscreen' (no quotes) or the account used to access the database must have permission to create new tables.";
    $_LANG["3SecurityNote"]                 = "<b>Important</b>: Using the 'root' account to access the database is highly discouraged. Please be aware that the username, database name <b>and password</b> will be saved as <b>unencrypted text</b> on the file system. Therefore, please make sure that no unauthorized access to the InfoScreen folder is possible.";

?>