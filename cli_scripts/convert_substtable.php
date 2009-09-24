<?php
/**
 * @version     2009-09-24
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
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
 *              
 * @note        If anyone understands this code -- and I mean REALLY understand --
 *              please contact me, because then you're probably a genius. Most of
 *              this I don't even understand myself when I look at it again after
 *              more than a week :)
 */
    //TODO: convert script: comment code more extensively
    //TODO: convert script: generate more console output
    
    //TODO: convert script: include language file and translate
    
    include_once ( __DIR__ . "/../config/config.php" );
    include_once ( __DIR__ . "/../includes/database.php" );
        
    setlocale(LC_TIME, 'de_DE', 'de_DE', 'deu_deu', 'de', 'ge'); //set locale (to get week day names in german)
    
    $outputdir = ($a = getValueByNameD("com_substtable_options", "default_output_dir", "")) ? $_SERVER["DOCUMENT_ROOT"] . $basepath . $a : dirname($argv[0]); //remember where to put the output files
    $tempdir = $_SERVER["DOCUMENT_ROOT"] . $basepath . getValueByNameD("com_substtable_options", "default_output_dir", "/temp/convert_substtable/");
    
    if ( !file_exists( $tempdir ) )
        mkdir( $tempdir, 0777, true );
    
    $verbose = false;
    $silent = false;
    $cron = false;
    $debug = false;
    
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
                echo "\tVerbose mode on.\n";
            } else if ( ($argv[$i] == '-s') || (strcasecmp($argv[$i], '--silent') == 0) ) {
                $silent = true;
                $verbose = false;
            } else if ( ($argv[$i] == '-c') || (strcasecmp($argv[$i], '--cron') == 0) ) {
                $silent = true;
                $cron = true;
                $verbose = false;
            } else if ( ($argv[$i] == '-d') || (strcasecmp($argv[$i], '--debug') == 0) ) {
                $debug = true;
                if ( !$silent )
                    echo "\tDebug mode on.\n";
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


    //function used to sort the classes in ascending order: 5-9, 10-11, Q11, K12, K13, Wahlf
    function classSort($a, $b) {
        global $debug;
        
        $letters = array('Q', 'K', 'W');
        //if $a is any of the classes 5-9, add a leading zero
        if ($a[0] != "1" && !( $al = in_array($a[0], $letters) )) {
            $a = "0" . $a;
        }
        //if $b is any of the classes 5-9, add a leading zero
        if ($b[0] != "1" && !( $bl = in_array($b[0], $letters) )) {
            $b = "0" . $b;
        }
        //handle special cases
        if ( $al || $bl ) {
            if ($al && !$bl)
                return 1;
            else if (!$al && $bl)
                return -1;
            else if ($al && $bl) {
                $result = array_search($a[0], $letters) - array_search($b[0], $letters);
                if ( $debug )
                    echo "$a - $b = $result\n";
                if ( $result != 0 )
                    return $result;
            }
        }
        return strcasecmp($a, $b);
    }
    
    function check_if_ignore($line) {  //TODO: convert script: make blacklist configurable (backend, database)
        if ( in_array( $line["Art"], array("Freisetzung", "Pausenaufsicht") ) )
            return true;
        
        if ( ($line["Art"] == "Sondereins.") && ($line["Vertretungs-Text"] != "entfällt!") )
            return true;
            
        if ( strcasecmp($line["(Klasse(n))"], "Pers") == 0 )
            return true;
        
        return false;
    }
    
    function read_file ($filename) {
        
        global $outputdir;
    
        if ( empty( $filename ) ) {
            echo "\tError: Empty filename\n";
            return false;
        } else if ( !is_file( $filename ) ) {
            echo "\tError: Filename invalid or file does not exist: '$filename'\n";
            return false;
        } else if ( !file_exists( $filename ) ) {
            echo "\tError: File does not exist: '$filename'\n";
            return false;
        } else if ( !is_readable( $filename ) ) {
            echo "\tError: File is not readable: '$filename'\n";
            return false;
        } else if ( !is_Writable( $outputdir ) ) {
            echo "\tError: Output directory ($outputdir) is not writable.\n";
            return false;
        } else {
            $subst_file = file( $filename ); //read the input file into an array
            if ( $subst_file === false ) {
                echo "\tError: There was an error while reading the input file ($filename).\n";
                return false;
            } else if ( empty( $subst_file ) ) {
                echo "\tError: The input file ($filename) is empty.\n";
                return false;
            }
        }
        
        if ( !$silent )
            echo "\tAnalyzing file '$filename' ...\n";
        
        //extract all headers
        $col_names = explode("\t", rtrim($subst_file[0], "\r\n\0"));
        //determine all the columns you want to keep here:
        $wanted_cols = array(/*"Datum", "Stunde",*/ "(Fach)", "Fach", "(Lehrer)", "Vertreter", /*"(Klasse(n))",*/ "(Raum)", "Raum", "Art", "Vertretungs-Text");
        //create the array of columns we have to delete
        $unwanted_cols = array_diff($col_names, $wanted_cols);
        
        for ($i = 1; $i < count($subst_file); $i++) {
            $line = $line_ = array_combine($col_names, explode("\t", rtrim($subst_file[$i], "\r\n\0"))); //create an associative array from the headres and the values
            foreach ( $unwanted_cols as $value )         //delete all unwanted items
                unset( $line[$value] );
            foreach ($line as $key => $entry)           //remove the place-holder string placed in empty cells
                if ( $entry == "'---" )
                    $line[$key] = "";
            if ( !check_if_ignore( $line_ ) )        //remove some items we dont want to display (actually, add only those we want)
                if ( strpos($line_["Klasse(n)"], ",") !== false ) {
                    $classes = explode(", ", $line_["Klasse(n)"]);
                    foreach ($classes as $class)
                        $table[$line_["Datum"]][strtoupper($class)][$line_["Stunde"]][] = $line;
                } else {
                    $table[$line_["Datum"]][strtoupper($line_["Klasse(n)"])][$line_["Stunde"]][] = $line;     //create our actual multi-dimensional array
                }
        }
        
        
        //pick only two days to display
        foreach ($table as $date => $thisDate) {
            $_date = strtotime(str_replace(".", "-", $date) . date("Y")); //modify time stamp so we can compare it
            if ( ( $_date >= time() ) && ( count( $_table ) < 2 ) ) {
                $_table[$_date] = $table[$date];
            }
        }
        
        if ( empty( $_table ) ) {
            echo "\tError: The input file ($filename) contained no data for today or a later day.\n";
            return false;
        }
    
        //sort the sub-arrays by class
        foreach ($_table as $date => $thisDate)
            uksort($_table[$date], "classSort");
        
        return $_table;
    
    }
        
    $table = read_file( $filename );
    if ( $table === false ) {
        echo "Quitting.\n\n";
        exit(1);
    }
    if ( time() < strtotime(date("Y-m-d" . " " . getValueByNameD("com_substtable_options", "highlight_changes_after", "07:00:00"))) ) {
        echo "\tCopying backup to check for changes later\n";
        copy( $filename, $tempdir . basename( $filename ) );
    } else if ( file_exists( $tempdir . basename( $filename ) ) ) {
        echo "\tComparing with backup\n";
        $old_table = read_file( $tempdir . basename( $filename ) );
        
        //compare backup and new table, and highlight changes, if any
        $newclasses = 0;
        $newlessons = 0;
        $newsubjects = 0;
        foreach ($table as $date => $thisDate) {
            if ( ($date == date("Y-m-d")) && (isset( $old_table[$date] )) ) {
                foreach ($thisDate as $class => $thisClass) {
                    if ( empty( $old_table[$date][$class] ) ) {
                        $isnew[$date][$class]["new"] = true;
                    } else {
                        foreach ($thisClass as $lesson => $thisLesson) {
                            if ( empty( $old_table[$date][$class][$lesson] ) ) {
                                $isnew[$date][$class][$lesson]["new"] = true;
                            } else {
                                foreach ($thisLesson as $key => $value) {
                                    if ( empty( $old_table[$date][$class][$lesson][$key] ) ) {
                                        $isnew[$date][$class][$lesson][$key]["new"] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ( $verbose ) {
            echo "\t\t" . ($newclasses + $newlessons + $newsubjects) . " found ($newclasses new classes, $newlessons new lessons, $newsubjects new subjects)\n";
        }
    } else {
        echo "\tNo backup found. Cannot check for changes\n";
    }
    
    
    $head = "";
    $head .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
    $head .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
    $head .= '<head>' . "\n";
    $head .= '    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />' . "\n";
    $head .= '    <meta name="author" content="Patrick Lehner" />' . "\n";
    $head .= '    <meta http-equiv="refresh" content="%%%%RELOAD_STRING%%%%" />' . "\n";
    $head .= '    <style type="text/css">' . "\n";
    $head .= '    <!--' . "\n";
    $head .= '        body { font-family: Arial, Verdana, sans-serif; }' . "\n";
    $head .= '        body.today    tr.even, body.today    td.class, body.today    td.lesson    { background: #FFFFDD; }' . "\n";
    $head .= '        body.today    tr.odd                                                      { background: #fff3ae; }' . "\n";
    $head .= '        body.tomorrow tr.even, body.tomorrow td.class, body.tomorrow td.lesson    { background: #DDFFDD; }' . "\n";
    $head .= '        body.tomorrow tr.odd                                                      { background: #beffbe; }' . "\n";
    $head .= '        body.other    tr.even, body.other    td.class, body.other    td.lesson    { background: #DDEEFF; }' . "\n";
    $head .= '        body.other    tr.odd                                                      { background: #D4E5F6; }' . "\n";
    $head .= '        table#main { border-collapse: collapse; width: 100%%%%; }' . "\n";
    $head .= '        td, th { border: 1px solid black; padding: 2px 4px; }' . "\n";
    $head .= '        tr.new, td.class.new, td.lesson.new { background: yellow !important; }' . "\n";
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
    $thead .= '            <tr class="even"><td colspan="10" style="text-align: center;"><span style="font-weight: bold;">%s</span>, Seite %d/%%d</td></tr>' . "\n";
    $thead .= '            <tr class="even"><th rowspan="2">Kl</th><th rowspan="2">Std.</th><th colspan="3">Planm&auml;&szlig;ig</th><th colspan="5">Vertretung</th></tr>' . "\n";
    $thead .= '            <tr class="even"><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Fach</td><td>Lehrer</td><td>Raum</td><td>Art</td><td>Grund</td></tr>' . "\n";
    
    $tfoot = "";
    $tfoot .= '        </tbody>' . "\n";
    $tfoot .= '    </table>' . "\n";

    
    $startlines = (int) getValueByNameD("com_substtable_options", "start_lines", 3);
    $maxlines = (int) getValueByNameD("com_substtable_options", "max_lines", 40);
    $lines = 0;
    $pages = 0;
    
    if ( !$silent )
        echo "\tCreating HTML...\n";
        
    $output[0] = "";
    foreach ($table as $date => $thisDate) {
        if ( $date == date("Y-m-d") ) {
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
            if ( $verbose ) {
                echo "\t\tLast page is page " . ($pages+1) . " with $lines lines\n";
            }
            $pages = 0;
            unset( $output );
            $output[0] = "";
        }
        if ( empty( $output[$pages] ) ) {
            if ( $verbose ) {
                echo "\tStarting new entry for $date...\n";
            }
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
            if ( $c > 1 )
                $prefix .= " rowspan=\"$c\"";
            if ( $isnew[$date][$class]["new"] ) {
                $classnew = " new";
            } else {
                $classnew = "";
            }
            $prefix .= " class=\"class$classnew\">$class</td>";
            foreach ($thisClass as $lesson => $thisLesson) {
                $prefix .= "<td";
                if ( count($thisLesson) > 1 )
                    $prefix .= " rowspan=\"" . count($thisLesson) . "\"";
                if ( $isnew[$date][$class][$lesson]["new"] || $classnew ) {
                    $lessonnew = " new";
                } else {
                    $lessonnew = "";
                }
                $prefix .= " class=\"lesson$lessonnew\">$lesson</td>";
                foreach ($thisLesson as $key => $value) {
                    $templines++;
                    if ( ($lines + $templines) % 2 )
                        $lineclass = "odd";
                    else
                        $lineclass = "even";
                    if ( $isnew[$date][$class][$lesson][$key]["new"] || $lessonnew )
                        $lineclass .= " new";
                    $tempout .= "            <tr class=\"$lineclass\">" . $prefix .             //TODO: convert script: make columns configurable
                                "<td>" . $value["(Fach)"] . "</td>" .
                                "<td>" . $value["(Lehrer)"] . "</td>" .
                                "<td>" . $value["(Raum)"] . "</td>" .
                                "<td>" . $value["Fach"] . "</td>" .
                                "<td>" . $value["Vertreter"] . "</td>" .
                                "<td>" . $value["Raum"] . "</td>" .
                                "<td>" . $value["Art"] . "</td>" .
                                "<td>" . $value["Vertretungs-Text"] . "</td>" .
                                "</tr>\n";
                    $prefix = "";
                }
            }
            if (($lines + $templines) >= $maxlines) {   //if the lines for this class dont fit onto this page anymore...
                if ( $verbose ) {
                    echo "\t\tPage " . ($pages + 1) ." full ($lines lines), next item is $templines lines\n";
                }
                if ( !($lines % 2) ) {
                    $tempout = str_replace("class=\"even", "class=\"even_", $tempout);
                    $tempout = str_replace("class=\"odd", "class=\"even", $tempout);
                    $tempout = str_replace("class=\"even_", "class=\"odd", $tempout);
                }
                $output[$pages] .= $tfoot . $foot;      //add the footer of this page
                $pages++;                               //increment the pages counter
                $output[$pages] = sprintf($head, $bodyClass) . sprintf($thead, $dateText, $pages + 1);  //start a new page and add its head
                $lines = $startlines;                   //reset the lines counter
            }
            $output[$pages] .= $tempout;
            $lines += $templines;
        }
    }
    if ( $verbose ) {
        echo "\t\tLast page is page " . ($pages+1) . " with $lines lines\n";
    }
    $output[$pages] .= $tfoot . $foot;
    $_output[] = $output;
    
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
            file_put_contents( $outputdir . "/" . $filenames[$c++], sprintf( $_output[$j][$i], $count ) );   //ATTENTION!!!! watch out for any %'s you used in the HTML!!
        }                                                                                                           //TODO: convert script: move template to file

    if ( !$cron ) {
        echo "Success.\n\n";
    } else {
        $log = sprintf("[%s] Input file: '%s'; wrote %d pages; Success.\n", date("Y-m-d h:n:s"), $filename, $j);
        file_put_contents( dirname($argv[0]) . "/../logs/convert_substtable.log", $log, FILE_APPEND );
    }
?>