<?php
/**
 * @version     2010-03-18
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009-2010 Patrick Lehner
 * @module      replace multiple substrings
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

function str_replace_multi ($search, $replace, $subject) {
    if ( !is_array($search) || !is_array($replace) )
        return false;
        
    if ( empty($subject) || count($search) == 0 || count($replace) == 0 )
        return "";
        
    $search = array_values($search);
    $replace = array_values($replace);
        
    if ( count($search) < count($replace) )
        $replace = array_slice( $replace, 0, count($search), true );
    else if ( count($search) > count($replace) )
        $search = array_slice( $search, 0, count($replace), true );
        
    for ( $i = 0; $i < count($search); $i++ ) {
        $subject = str_replace( $search[$i], $replace[$i], $subject );
    }
    
    return $subject;
}

?>