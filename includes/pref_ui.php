<?php
/**
 * @version     2010-06-04
 * @author      Quan Tran
 * @author      Patrick Lehner <lehner.patrick@gmx.de>
 * @copyright   Copyright (C) 2009-2010 Quan Tran, Patrick Lehner
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

    defined("__MAIN") or die ("Restricted Access.");
    $table = $db->getTables();
    // this is a list of elements we shouldn't be able
    // to modify
    $noModify = array("id");
    
?>

<style type="text/css">
<!--

div#prefsui label, div#prefsui input 
{
        display: block;
        width: 250px;
        float: left;
        margin-bottom: 10px;
}

div#prefsui label 
{
        text-align: right;
        width: 75px;
        padding-right: 20px;
}

div#prefsui br 
{
        clear: left;
}

-->
</style>


<div id="prefsui">

<center><big><b>Tables:</b></big></center>

<?php
    
    // this is the update rows in the table
    if (isset($_POST["save"]))
    {
        $error = FALSE;
        $select = $_GET["select_table"];
        $elements = $db->query("SELECT * from $select");
        $typeres = $db->query("DESCRIBE $select");
        if ($typeres === FALSE)
        {
            $handler->addMessage("Preferences UI", 
                "Failed to select anything from the database!");
        }

        while ($type[] = mysql_fetch_assoc($typeres));
        unset($typeres);

        while ($element = mysql_fetch_assoc($elements))
        {
            $tags = array_keys($element);
            $command = "UPDATE $select SET ";
            $i = 0;

            $numericTypeTable = array("int");
            foreach ($tags as $v)
            {
                if (array_search($v, $noModify) === FALSE)
                {
                    $t = $v . "_" . $element["id"];
                    $c = "`$v`='$_POST[$t]',";
                    foreach ($numericTypeTable as $j)
                    { 
                        if (stristr($type[$i]["Type"], $j) 
                             !== FALSE)
                        {
                            $c = "`$v`=$_POST[$t],";
                            break;
                        }
                    }
                    $command .= $c;
                }
               $i++; 
            }
            $t = $element["id"];
            // to get rid of the comma in the loop above
            $command[strlen($command) - 1] = ' ';
            $command .= "WHERE id=$t";
            
            //print $command;
            if ($db->query($command) === FALSE)
                $error = TRUE;
  
        }
        if ($error == TRUE)
            $handler->addMsg("Preferences UI", "Failed to save the database!");  
        else
            $handler->addMsg("Preferences UI", "Saved Successfully!");  
        
        unset($type);
    }
?>

<?php
    
    // this is the printing of the tables and such 

    $link = "?component=prefsui&select_table";
    $location = $link . "=";

    // this prints out the table
    echo '<ul>';
    foreach ($table as $values)
    {
        echo '<li>' . "<a href=\"$location$values\">$values</a>" 
              . '</li>';
    }
    echo '</ul>';
    unset($link);
?>

<?php
    // this is when the user clicks on a table, and the elements
    // are shown

    // which table the user selected
    $select = $_GET["select_table"];
    // see if the user actually selected a table
    if (strcmp($select, "") == 0)
        return;
    
    // see if the table actually exists 
    if ($db->query("DESCRIBE $select") == FALSE)
    {
        echo "<big><b>Cannot find table \"$select\" </big></b>";
        return;
    }
    // will contain the link to the page
    $location .= $select; 

    echo '<center><big><b>Elements:</big></b></center><hr/>';
    
    // get all the elements in the mysql database
    $elements = $db->query("SELECT * from $select");   
    if ($elements === FALSE)
    {
        $handler->addMessage("Preferences UI", 
            "Failed to select anything from the database!");
    }

    echo "<form input=$location method=POST>";
    while ($element = mysql_fetch_assoc($elements))
    {
        $tags = array_keys($element);
        for ($i = 0, $k = count($element); $i < $k; $i++)
        {
            print "<label for=$tags[$i]>$tags[$i]:</label>";
            // for read only data
            if (array_search($tags[$i], $noModify) !== FALSE)
            {
                printf("%s", $element[$tags[$i]]);
            }
            else // modifable data
            {
                printf("<input name=\"%s\", type=\"text\" name=\"%s\" value=\"%s\"/>",
                        $tags[$i] . "_" . $element["id"], $tags[$i], $element[$tags[$i]]);
            }
            echo '</td>
                  <br/>';
        }
        echo '<br/>
              <hr/>';
    }
    echo '<input name="save" type="submit" value="Save" /><br />';
    echo '</form>';
?>

</div>
