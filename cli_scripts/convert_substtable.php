<?php
/**
 * @version     2009-09-11
 * @author      Patrick Lehner
 * @copyright   Copyright (C) 2009 Patrick Lehner
 * @module      CLI script to convert the substitution table to displayable HTML
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
    
    echo "\nAnalyzing and converting substitution table...\n";
        
    setlocale(LC_TIME, 'de_DE@euro', 'de_DE', 'deu_deu');

    //read the input file into an array
    $subst_file = file("c:/xampp/htdocs/infoscreen/upload/substitution.txt");
    
    //extract all headers
    $heading = explode("\t", rtrim($subst_file[0], "\r\n\0"));
    //determine all the columns you want to keep here:
    $wanted_heads = array(/*"Datum", "Stunde",*/ "(Fach)", "Fach", "(Lehrer)", "Vertreter", /*"(Klasse(n))",*/ "(Raum)", "Raum", "Art", "Vertretungs-Text");
    //create the array of columns we have to delete
    $unwanted_heads = array_diff($heading, $wanted_heads);
    
    for ($i = 1; $i < count($subst_file); $i++) {
        $line = $line_ = array_combine($heading, explode("\t", rtrim($subst_file[$i], "\r\n\0"))); //create an associative array from the headres and the values
        foreach ($unwanted_heads as $value)         //delete all unwanted items
            unset($line[$value]);
        foreach ($line as $key => $entry)           //remove the place-holder string placed in empty cells
            if ( $entry == "'---" )
                $line[$key] = "";
        if (!in_array($line_["Art"], array("Freisetzung", "Sondereins.", "Pausenaufsicht")))        //remove some items we dont want to display
            $table[$line_["Datum"]][$line_["(Klasse(n))"]][$line_["Stunde"]][] = $line;     //create out actual multi-dimensional array
    }
    
    
    //pick only two days to display
    $today = date("Y-m-d");
    foreach ($table as $date => $thisDate) {
        //echo "$date\n";
        $_date = date("Y-m-d", strtotime(str_replace(".", "-", $date) . date("Y")));
        //echo "$_date\n";
        if ( ( $_date >= $today ) && ( count( $_table ) < 2 ) ) {
            $_table[$_date] = $table[$date];
        }
    }
    
    //var_dump($_table);
    
    if ( empty( $_table ) ) {
        //TODO: handle error
        echo "The input file contained no data for today or a later day.";
    } else {
        
        $table = $_table;
        
        foreach ($table as $date => $thisDate) {
            if ( $date == $today ) {
                $summary[] = "<span class=\"today%%s\">Heute, " . date("d.m.", strtotime($date)) . ": %d</span>";
            } else if ( $date == date("Y-m-d", strtotime("+1day")) ) {
                $summary[] = "<span class=\"tomorrow%%s\">Morgen, " . date("d.m.", strtotime($date)) . ": %d</span>";
            } else {
                $summary[] = "<span class=\"other%%s\">" . strftime("%A, %d.%m.", strtotime($date)) . ": %d</span>";
            }
        }
    
    
    
        //function used to sort the classes in ascending order: 5-9, 10-11, K12, K13, Wahlf
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
        
        //sort the sub-arrays by class
        foreach ($table as $key => $value)
            uksort($table[$key], "classSort");
            
        unset($subst_file);
        
        
        $head = "";
        $head .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
        $head .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
        $head .= '<head>' . "\n";
        $head .= '    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />' . "\n";
        $head .= '    <meta name="author" content="Patrick Lehner" />' . "\n";
        $head .= '    <meta http-equiv="refresh" content="10; URL=%s" />' . "\n";
        $head .= '    <style type="text/css">' . "\n";
        $head .= '    <!--' . "\n";
        $head .= '        table#main { border-collapse: collapse; }' . "\n";
        $head .= '        table#summary { width: 100%%; }' . "\n";
        $head .= '        td, th { border: 1px solid black; padding: 2px 4px; }' . "\n";
        $head .= '        table#summary td, table#summary th { border: 0 none; padding: 0; }' . "\n";
        $head .= '        table#summary span + span { margin-left: 20px; }' . "\n";
        $head .= '        span.today { background-color: #FFD; }' . "\n";
        $head .= '        span.tomorrow { background-color: #DFD; }' . "\n";
        $head .= '        span.other { background-color: #DDF; }' . "\n";
        $head .= '        span.current { font-weight: bold; }' . "\n";
        $head .= '    -->' . "\n";
        $head .= '    </style>' . "\n";
        $head .= '    <title>Vertretungsplan</title>' . "\n";
        $head .= '</head>' . "\n";
        $head .= '<body>' . "\n";
        
        $foot = "";
        $foot .= '</body>' . "\n";
        $foot .= '</html>' . "\n";
        
        $thead = "";
        $thead .= '    <table id="main" border="0" cellspacing="0" cellpadding="0">' . "\n";
        $thead .= '        <tbody>' . "\n";
        //$thead .= '            <tr><td style="padding-bottom: 10px; text-align: center;" colspan="10"><span style="margin: 0 20px; background-color: white;">Heute: 4</span> <span style="margin: 0 20px; background-color: #FFD; color: gray; font-style: italic;">(Morgen: 0)</span> <span style="margin: 0 20px; background-color: #DFD;">Montag: 2</span></td></tr>' . "\n";
        $thead .= '            <tr><td colspan="10"><table id="summary" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td style="text-align: left;">%%s</td><td style="text-align: right;"><span style="font-weight: bold;">%s</span>, Seite %d/%%d</td></tr></tbody></table></td></tr>' . "\n";
        $thead .= '            <tr><th rowspan="2">Kl</th><th rowspan="2">Std.</th><th colspan="3">Planm&auml;&szlig;ig</th><th colspan="5">Vertretung</th></tr>' . "\n";
        $thead .= '            <tr><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Art</td><td>Grund</td></tr>' . "\n";
        
        $tfoot = "";
        $tfoot .= '        </tbody>' . "\n";
        $tfoot .= '    </table>' . "\n";
    
        
        $startlines = 3;
        $maxlines = 40;
        $lines = 0;
        $pages = 0;
        
        $output[0] = "";
        foreach ($table as $date => $thisDate) {
            $internaldate = strtotime(str_replace(".", "-", $date) . date("Y"));
            if ( !empty( $output[$pages] ) ) {
                $output[$pages] .= $tfoot . $foot;
                $_output[] = $output;
                $pages = 0;
                $output[0] = "";
            }
            if ( empty( $output[$pages] ) ) {
                $output[$pages] = $head . sprintf($thead, strftime("%A, %d.%m.", $internaldate), $pages + 1);
                $lines = $startlines;
            }
            foreach ($thisDate as $class => $thisClass) {
                $tempout = "";
                $templines = 0;
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
                        $templines++;
                        $tempout .= "            <tr>" . $prefix . "<td>" . $value["(Fach)"] . "</td><td>" . $value["(Lehrer)"] . "</td><td>" . $value["(Raum)"] . "</td><td>" . $value["Fach"] . "</td><td>" . $value["Vertreter"] . "</td><td>" . $value["Raum"] . "</td><td>" . $value["Art"] . "</td><td>" . $value["Vertretungs-Text"] . "</td></tr>\n";
                        $prefix = "";
                    }
                }
                if (($lines + $templines) >= $maxlines) {
                    $output[$pages] .= $tfoot . $foot;
                    $pages++;
                    $output[$pages] = $head . sprintf($thead, strftime("%A, %d.%m.", $internaldate), $pages + 1);
                    $lines = $startlines;
                }
                $output[$pages] .= $tempout;
                $lines += $templines;
            }
        }
        $output[$pages] .= $tfoot . $foot;
        $_output[] = $output;
        
        switch ( count ( $summary ) ) {
            case 0:
                break;
            case 1:
                $summary = sprintf ( $summary[0], count( $_output[0] ) );
                break;
            case 2:
            default:
                $summary = sprintf ( $summary[0], count( $_output[0] ) ) . " " . sprintf ( $summary[1], count( $_output[1] ) );
        }
        
        for ($j = 0; $j < count( $_output ); $j++) 
            for ($i = 0; $i < ( $count = count( $_output[$j] ) ); $i++)
                $filenames[] = sprintf("Day%d_%d.html", $j, $i);
        $filenames[] = $filenames[0];
        
        $c = 0;
        for ($j = 0; $j < count( $_output ); $j++) 
            for ($i = 0; $i < ( $count = count( $_output[$j] ) ); $i++) {
                file_put_contents( $filenames[$c++], sprintf( $_output[$j][$i], $filenames[$c], sprintf($summary, ($j==0)?" current":"", ($j==1)?" current":""), $count ) );   //ATTENTION!!!! watch out for any %'s you used in the HTML!!
            }
    
    
        echo "Done.\n";
    }
?>