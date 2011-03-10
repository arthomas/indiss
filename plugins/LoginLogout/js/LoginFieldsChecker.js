/**
 * @version     2011-03-10
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2011 Patrick Lehner
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

function checkLoginFields() {
	u = document.getElementById("username");
	p = document.getElementById("pw");
	var r = true;

	if (u.value.length == 0) {
	    r = false;
	    u.style.backgroundColor = "#FBB";
	} else {
	    u.style.backgroundColor = "transparent";
	}

	if (p.value.length == 0) {
        r = false;
        p.style.backgroundColor = "#FBB";
    } else {
        p.style.backgroundColor = "transparent";
    }

    if (!r) {
        document.getElementById("errorrow").style.display = "table-row";
    } else {
        document.getElementById("errorrow").style.display = "none";
    }

    return r;
}