<?php
/**
 * @version     2009-09-10
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      TEMPORARY component list
 * 
 * @note        This serves as a work-around while the components are both in /admin/components and
 *              /components/com_xxxx ; this file and its used should be removed once migration to
 *              the latter is complete.
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

    //TODO: remove this file once migration is complete
    
    defined("__MAIN") or die("Restricted access.");
 
    // new
    $_comlist["tickers"]        = "../components/com_tickers/admin.php";
    $_comlist["content"]        = "../components/com_content/admin.php";
    
    //old
    $_comlist["comtest"]        = "components/comtest.php";
    $_comlist["fckeditor"]      = "components/fckeditor.php";
    $_comlist["login"]          = "components/login.php";
    $_comlist["overview"]       = "components/overview.php";
    $_comlist["registeruser"]   = "components/registeruser.php";
    $_comlist["settings"]       = "components/settings.php";
    $_comlist["substtable"]     = "components/substtable.php";
    

?>