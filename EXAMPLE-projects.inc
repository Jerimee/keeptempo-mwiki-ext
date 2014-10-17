<?php
/*
* the first value (ex "aaa") is the key and namecode of the project
* the project_id is a code for that project you get from tempo
* the rest set as desired
*/

$myprojects = array(
          "aaa" => array(
               "project_id" => 10000,
               "retAmount" => 100,
               "retLast" => 100,
               "hours" => 0,
               "month" => "OCT",
               "report_id" => 20000,
               "formalname" => "Acme Anchors Associated",
          ),
          "bbb" => array(
               "project_id" => 10001,
               "retAmount" => 20,
               "retLast" => 20,
               "hours" => 0,
               "month" => "OCT",
               "report_id" => 20002,
               "formalname" => "Blinged Barge Boat",
          ),
          "ccc" => array(
               "project_id" => 10002,
               "retAmount" => 10,
               "retLast" => 10,
               "hours" => 0,
               "month" => "OCT",
               "report_id" => 20001,
               "formalname" => "Clammy Clan Corp",
          ),
     );
     
     // all the ids 
     $myListOfProjects = array();
     $i=0;
 foreach($myprojects as $proj => $prec) {
     	$myListOfProjects[$i] = $prec["project_id"];
     	$i++;
    } 
 
     // special group one
     $mylistofkeys = array("aaa","bbb");

     // special group two
     $hourlylist = array("aaa","ccc");

?>
