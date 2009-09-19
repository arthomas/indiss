<?php
/**
 * @version     2009-09-16
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
    //TODO: convert script: comment code more extensively
    //TODO: convert script: generate more console output
    //TODO: convert script: include database and use it
    
    //TODO: convert script: include language file and translate
    
    //TODO: convert script: mark odd/even table rows
    //TODO: convert script: hightlight changes after 7am
        
    setlocale(LC_TIME, 'de_DE', 'de_DE', 'deu_deu', 'de', 'ge'); //set locale (to get week day names in german)
    
    $outputdir = dirname($argv[0]); //remember where to put the output files  //TODO: convert script: fetch default from database
    
    $verbose = false;
    $silent = false;
    $cron = false;
    
    for ($i = 1; $i < $argc; $i++) {
        if ( $argv[$i][0] == '-' ) {
            if ( ($argv[$i] == '-V') || (strcasecmp($argv[$i], '--version') == 0) ) {
                //output version info
            } else if ( strcasecmp($argv[$i], '--usage') == 0 ) {
                //output usage info
            } else if ( ($argv[$i] == '-h') || ($argv[$i] == '-?') || (strcasecmp($argv[$i], '--help') == 0) ) {
                //output help
            } else if ( ($argv[$i] == '-v') || (strcasecmp($argv[$i], '--verbose') == 0) ) {
                $verbose = true;
                $silent = false; 
            } else if ( ($argv[$i] == '-s') || (strcasecmp($argv[$i], '--silent') == 0) ) {
                $silent = true;
                $verbose = false;
            } else if ( ($argv[$i] == '-c') || (strcasecmp($argv[$i], '--cron') == 0) ) {
                $silent = true;
                $cron = true;
                $verbose = false;
            } else if ( ($argv[$i] == '-o') || (strcasecmp($argv[$i], '--output') == 0) ) {
                if ( $argv[$i+1][0] != '-' )
                    $outputdir = $argv[++$i];
            } else if ( ($argv[$i] == '-i') || (strcasecmp($argv[$i], '--input') == 0) ) {
                if ( $argv[$i+1][0] != '-' )
                    $filename = $argv[++$i];
            } else if ( ($argv[$i] == '-l') || (strcasecmp($argv[$i], '--locale') == 0) ) {
                if ( $argv[$i+1][0] != '-' )
                    $locale = $argv[++$i];
            }
        } else {
            if ( $i == $argc-1 ) {
                $filename = $argv[$i];
            } else if ( !$silent )
                echo "\tError: Unknown option '$argv[$i]'\n";
        }
    }
    
    //$filename = $argv[$i];
    if ( empty( $filename ) ) {
        echo "\tError: Empty filename\nQuitting.\n\n";
        exit(1);
    } else if ( !is_file( $filename ) ) {
        echo "\tError: Filename invalid or file does not exist: '$filename'\nQuitting.\n\n";
        exit(1);
    } else if ( !file_exists( $filename ) ) {
        echo "\tError: File does not exist: '$filename'\nQuitting.\n\n";
        exit(1);
    } else if ( !is_readable( $filename ) ) {
        echo "\tError: File is not readable: '$filename'\nQuitting.\n\n";
        exit(1);
    } else if ( !is_Writable( $outputdir ) ) {
        echo "\tError: Output directory ($outputdir) is not writable.\nQuitting.\n\n";
        exit(1);
    } else {
        $subst_file = file( $filename ); //read the input file into an array
        if ( $subst_file === false ) {
            echo "\tError: There was an error while reading the input file ($filename).\nQuitting.\n\n";
            exit(1);
        } else if ( empty( $subst_file ) ) {
            echo "\tError: The input file ($filename) is empty.\nQuitting.\n\n";
            exit(1);
        }
    }
    
    if ( !$silent )
        echo "\tAnalyzing file '$filename' ...\n";    
    
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
            $table[$line_["Datum"]][strtoupper($line_["Klasse(n)"])][$line_["Stunde"]][] = $line;     //create our actual multi-dimensional array
    }
    
    
    //pick only two days to display
    $today = date("Y-m-d");
    foreach ($table as $date => $thisDate) {
        $_date = date("Y-m-d", strtotime(str_replace(".", "-", $date) . date("Y"))); //modify time stamp so we can compare it
        if ( ( $_date >= $today ) && ( count( $_table ) < 2 ) ) {
            $_table[$_date] = $table[$date];
        }
    }
    
    //var_dump($_table);
    
    if ( empty( $_table ) ) {
        //TODO: handle error
        echo "\nError: The input file ($filename) contained no data for today or a later day.\nQuitting.\n\n";
        exit(1);
    }
        
    $table = $_table;


    //function used to sort the classes in ascending order: 5-9, 10-11, Q11, K12, K13, Wahlf
    function classSort($a, $b) {
        $letters = array('Q', 'K', 'W');
        //if $a is any of the classes 5-9, add a leading zero
        if ($a[0] != "1" && !( $al = in_array($a[0], $letters) ))
            $a = "0" . $a;
        //if $b is any of the classes 5-9, add a leading zero
        if ($b[0] != "1" && !( $bl = in_array($b[0], $letters) ))
            $b = "0" . $b;
        //handle special cases
        if ( $al || $bl ) {
            if ($ak && !$bk)
                return 1;
            else if (!$ak && $bk)
                return -1;
            else {
                if ( ($a[0] == 'Q') && ($b[0] != 'Q') )
                    return 1;
                else if ( ($a[0] != 'Q') && ($b[0] == 'Q') )
                    return -1;
            }
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
    $head .= '    <meta http-equiv="refresh" content="15; URL=%%s" />' . "\n";
    $head .= '    <style type="text/css">' . "\n";
    $head .= '    <!--' . "\n";
    $head .= '        body { font-family: Arial, Verdana, sans-serif; }' . "\n";
    $head .= '        body.today td, body.today th { background: #FFD; }' . "\n";
    $head .= '        body.tomorrow td, body.tomorrow th { background: #DFD; }' . "\n";
    $head .= '        body.other td, body.other th { background: #DEF; }' . "\n";
    $head .= '        table#main { border-collapse: collapse; }' . "\n";
    $head .= '        td, th { border: 1px solid black; padding: 2px 4px; }' . "\n";
    $head .= '    -->' . "\n";
    $head .= '    </style>' . "\n";
    $head .= '    <title>Vertretungsplan</title>' . "\n";
    $head .= '</head>' . "\n";
    $head .= '<body class="%s">' . "\n";
    
    $foot = "";
    $foot .= '</body>' . "\n";
    $foot .= '</html>' . "\n";
    
    $thead = "";
    $thead .= '    <table id="main" border="0" cellspacing="0" cellpadding="0">' . "\n";
    $thead .= '        <tbody>' . "\n";
    //$thead .= '            <tr><td style="padding-bottom: 10px; text-align: center;" colspan="10"><span style="margin: 0 20px; background-color: white;">Heute: 4</span> <span style="margin: 0 20px; background-color: #FFD; color: gray; font-style: italic;">(Morgen: 0)</span> <span style="margin: 0 20px; background-color: #DFD;">Montag: 2</span></td></tr>' . "\n";
    $thead .= '            <tr><td colspan="10" style="text-align: center;"><span style="font-weight: bold;">%s</span>, Seite %d/%%d</td></tr>' . "\n";
    $thead .= '            <tr><th rowspan="2">Kl</th><th rowspan="2">Std.</th><th colspan="3">Planm&auml;&szlig;ig</th><th colspan="5">Vertretung</th></tr>' . "\n";
    $thead .= '            <tr><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Art</td><td>Grund</td></tr>' . "\n";
    
    $tfoot = "";
    $tfoot .= '        </tbody>' . "\n";
    $tfoot .= '    </table>' . "\n";

    
    $startlines = 3;                //TODO: convert script: move parameters to database
    $maxlines = 40;
    $lines = 0;
    $pages = 0;
    
    if ( !$silent )
        echo "\tCreating HTML...\n";
    
    $output[0] = "";
    foreach ($table as $date => $thisDate) {
        //$internaldate = strtotime(str_replace(".", "-", $date) . date("Y"));
        if ( $date == $today ) {
            $dateText = "Heute, " . date("d.m.", strtotime($date));
            $bodyClass = "today";
        } else if ( $date == date("Y-m-d", strtotime("+1day")) ) {
            $dateText = "Morgen, " . date("d.m.", strtotime($date));
            $bodyClass = "tomorrow";
        } else {
            $dateText = strftime("%A, %d.%m.", strtotime($date));
            $bodyClass = "other";
        }
        if ( !empty( $output[$pages] ) ) {
            $output[$pages] .= $tfoot . $foot;
            $_output[] = $output;
            $pages = 0;
            $output[0] = "";
        }
        if ( empty( $output[$pages] ) ) {
            $output[$pages] = sprintf($head, $bodyClass) . sprintf($thead, $dateText, $pages + 1);
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
                $output[$pages] = sprintf($head, $bodyClass) . sprintf($thead, $dateText, $pages + 1);
                $lines = $startlines;
            }
            $output[$pages] .= $tempout;
            $lines += $templines;
        }
    }
    $output[$pages] .= $tfoot . $foot;
    $_output[] = $output;
    
    //$verbose = true;
    
    //delete old output files
    if ( !$silent )
        echo "\tDeleting old output files...\n";
    $files = scandir($outputdir);
    foreach (preg_grep("/^Day\d_\d.html$/", $files) as $file) {
        if ( $verbose )
            echo "\t\t$outputdir/$file  -  ";
        $result = unlink ($outputdir . "/" . $file);
        if ( $verbose )
            echo ( $result ) ? "Deleted!\n" : "Failed!\n";
    }
    
    //create array with the names of the output files
    for ($j = 0; $j < count( $_output ); $j++) 
        for ($i = 0; $i < ( $count = count( $_output[$j] ) ); $i++)
            $filenames[] = sprintf("Day%d_%d.html", $j, $i);
    $filenames[] = $filenames[0];
    
    if ( !$silent )
        echo "\tWriting output files...\n";
    $c = 0;
    for ($j = 0; $j < count( $_output ); $j++) 
        for ($i = 0; $i < ( $count = count( $_output[$j] ) ); $i++) {
            file_put_contents( $outputdir . "/" . $filenames[$c++], sprintf( $_output[$j][$i], $filenames[$c], $count ) );   //ATTENTION!!!! watch out for any %'s you used in the HTML!!
        }                                                                                                           //TODO: convert script: move template to file

    if ( !$cron ) {
        echo "Success.\n\n";
    } else {
        $log = sprintf("[%s] Input file: '%s'; wrote %d pages; Success.\n", date("Y-m-d h:n:s"), $filename, $j);
        file_put_contents( dirname($argv[0]) . "/../logs/convert_substtable.log", $log, FILE_APPEND );
    }
?>