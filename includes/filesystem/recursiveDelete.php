<?php
/**
 * @version     2010-05-03
 * @author      Eddy Vlad
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009 Eddy Vlad
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
 *              
 * @note        This function was taken from a comment on the php.net article on
 *              unlink() (http://de.php.net/manual/en/function.unlink.php), posted 
 *              by Eddy Vlad on 2009-11-04 04:59. As of 2010-05-03 the comment is 
 *              available at http://de.php.net/manual/en/function.unlink.php#94766
 */
 
/**
 * Delete a file or recursively delete a directory
 *
 * @param string $str Path to file or directory
 */
function recursiveDelete($str){
    if(is_file($str)){
        return @unlink($str);
    }
    elseif(is_dir($str)){
        $scan = glob(rtrim($str,'/').'/*');
        foreach($scan as $path){
            recursiveDelete($path);
        }
        return @rmdir($str);
    }
}

?>