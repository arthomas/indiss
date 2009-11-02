<?php
/**
 * @version     2009-11-02
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      list of languages; for installation script
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
 *              
 * @note        This is a list of all available languages. The array key is used as
 *              internal identifier and should be unique to each entry. The value of
 *              each entry is the name of the language as it will be displayed;
 *              usually you would put the name of the language in that language here.
 *              Also, make sure that there is a folder with *only* the key of each
 *              value declared here, located in the directory of this file, with all
 *              files needed inside that folder. (There will probably be fail-safes
 *              in case a language file doesn't exist, but hey, no guarantees.)
 *              Example: Language English, language code "en"; File tree:
 *               lang
 *                \- en
 *                    +- en.php
 *                    \- license.txt
 */

    $languages["en"]="English";
    //$languages["de"]="Deutsch";
    
?>
