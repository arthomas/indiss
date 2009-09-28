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

    defined("__MAIN") or die("Restricted access.");
    
    $subst_file = file("c:/xampp/htdocs/infoscreen/upload/substitution.txt");
    
    $heading = explode("\t", rtrim($subst_file[0], "\r\n\0"));
    $wanted_heads = array(/*"Datum", "Stunde",*/ "(Fach)", "Fach", "(Lehrer)", "Vertreter", /*"(Klasse(n))",*/ "(Raum)", "Raum", "Art", "Vertretungs-Text");
    
    $unwanted_heads = array_diff($heading, $wanted_heads);
    
    for ($i = 1; $i < count($subst_file); $i++) {
        $line = $line_ = array_combine($heading, explode("\t", rtrim($subst_file[$i], "\r\n\0")));
        foreach ($unwanted_heads as $value)
            unset($line[$value]);
        if (!in_array($line_["Art"], array("Freisetzung", "Pausenaufsicht")))
            $table[$line_["Datum"]][$line_["(Klasse(n))"]][$line_["Stunde"]][] = $line;
    }
    
    function classSort($a, $b) {
        //if $a is any of the classes 5-9, add a leading zero
        if ($a[0] != "1" && $a[0] != "K" && $a[0] != "W")
            $a = "0" . $a;
        //if $b is any of the classes 5-9, add a leading zero
        if ($b[0] != "1" && $b[0] != "K" && $b[0] != "W")
            $b = "0" . $b;
        //if (exclusively) $a or $b is K12 or K13, that one comes always after classes 5-11
        if (($ak = ( ($a[0] == "K") || (($a[0] == "W")) )) xor ($bk = ( ($b[0] == "K") || (($b[0] == "W")) ))) {
            if ($ak && !$bk)
                return 1;
            elseif (!$ak && $bk)
                return -1;
        }
        return strcasecmp($a, $b);
    }
    
    foreach ($table as $key => $value)
        uksort($table[$key], "classSort");
        
    unset($subst_file);

    echo ""
?>
<style>
<!--
    table { border-collapse: collapse; }
    td, th { border: 1px solid black; padding: 2px 4px; }
-->
</style>
<?php foreach ($table as $date => $thisDate) { ?>
<table border="0" cellspacing="0" cellpadding="0">
    <tr><td style="padding-bottom: 10px; text-align: center;" colspan="10"><span style="margin: 0 20px; background-color: white;">Heute: 4</span> <span style="margin: 0 20px; background-color: #FFD; color: gray; font-style: italic;">(Morgen: 0)</span> <span style="margin: 0 20px; background-color: #DFD;">Montag: 2</span></td></tr>
    <tr><th colspan="10">Datum: <?php echo $date; ?></th></tr>
    <tr><th rowspan="2">Kl</th><th rowspan="2">Std.</th><th colspan="3">Planm&auml;&szlig;ig</th><th colspan="5">Vertretung</th></tr>
    <tr><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Art</td><td>Grund</td></tr>
    <?php foreach ($thisDate as $class => $thisClass) {
        $prefix  = "<td";
        $c = 0;
        foreach ($thisClass as $thisLesson)
            $c += count($thisLesson);
        $prefix .= ($c > 1) ? " rowspan=\"$c\"" : "" ;
        $prefix .= ">$class</td>";
        foreach ($thisClass as $lesson => $thisLesson) {
            $prefix .= "<td";
            $prefix .= (count($thisLesson) > 1) ? " rowspan=\"" . count($thisLesson) . "\"" : "" ;
            $prefix .= ">$lesson</td>";
            foreach ($thisLesson as $value) {
                echo "<tr>" . $prefix . "<td>" . $value["(Fach)"] . "</td><td>" . $value["(Lehrer)"] . "</td><td>" . $value["(Raum)"] . "</td><td>" . $value["Fach"] . "</td><td>" . $value["Vertreter"] . "</td><td>" . $value["Raum"] . "</td><td>" . $value["Art"] . "</td><td>" . $value["Vertretungs-Text"] . "</td></tr>\n";
                $prefix = "";
            }
        }
    }?>
</table><br /><br />
<?php } ?>
