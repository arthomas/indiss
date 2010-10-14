/**
 * @version     2010-10-14
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2010 Patrick Lehner
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

function doSubmitMultiple(postview, task) {
    l = new Array();
    a = document.getElementsByTagName('input');
    for (i=0;i<a.length;i++) {
        if (a[i].type=='checkbox' && a[i].checked && a[i].name!='') {
            l = l.concat(a[i].name.substr(6));
        }
    }
    if (l.length > 0) {
        ai.value = l.join(",");
        pv.value = postview;
        if (task != '') {
            form.action = form.action + '&task=' + task;
        }
        form.submit();
    }
}

function doSubmitSingle(affected, postview, task) {
    ai.value = affected;
    pv.value = postview;
    if (task != '') {
        form.action = form.action + '&task=' + task;
    }
    form.submit();
}