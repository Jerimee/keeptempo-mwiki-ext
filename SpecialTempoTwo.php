<?php
class SpecialTempoTwo extends SpecialPage {
     function __construct() {
          parent::__construct( 'Tempotwo' );
     }
 
     function execute( $par ) {
          $request = $this->getRequest();
          $output = $this->getOutput();
          $this->setHeaders(); 
          # Get request data from, e.g.
          $param = $request->getText( 'param' );
          # Do stuff
          loadTempoTwoData();
     }
}

function loadTempoTwoData() {
/*
     TODO: Restructure the program to use this format
     
     $projects = array(
          "ANC" => array(
               "project_id" => 17933,
               "retAmount" => 30,
               "retLast" => 20,
               "hours" => 0,
          ),
     );
*/



$myprojects = array(
          "anc" => array(
               "project_id" => 17933,
               "retAmount" => 44,
               "retLast" => 20,
               "hours" => 0,
               "report_id" => 23742,
          ),
          "bmb" => array(
               "project_id" => 11043,
               "retAmount" => 10,
               "retLast" => 10,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "cc" => array(
               "project_id" => 20893,
               "retAmount" => 39,
               "retLast" => 45,
               "hours" => 0,
               "report_id" => 28622,
          ),
          "iss" => array(
               "project_id" => 11446,
               "retAmount" => 50,
               "retLast" => 50,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "mvfr" => array(
               "project_id" => 24757,
               "retAmount" => 15,
               "retLast" => 12,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "supgv" => array(
               "project_id" => 26161,
               "retAmount" => 49,
               "retLast" => 50,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "awl" => array(
               "project_id" => 31593,
               "retAmount" => 33,
               "retLast" => 20,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "vic" => array(
               "project_id" => 27109,
               "retAmount" => 9,
               "retLast" => 10,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "dgb" => array(
               "project_id" => 33503,
               "retAmount" => 20,
               "retLast" => 16,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "han" => array(
               "project_id" => 33505,
               "retAmount" => 5,
               "retLast" => 10,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "pb" => array(
               "project_id" => 28954,
               "retAmount" => 10,
               "retLast" => 10,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "wcwc" => array(
               "project_id" => 33907,
               "retAmount" => 10,
               "retLast" => 10,
               "hours" => 0,
               "report_id" => 23582,
          ),
          "ro" => array(
               "project_id" => 17688,
               "retAmount" => 100,
               "retLast" => 100,
               "hours" => 0,
               "report_id" => 23582,
               "formalname" => "Rchir Outreach",
          ),
          "fdn" => array(
               "project_id" => 32007,
               "retAmount" => 100,
               "retLast" => 100,
               "hours" => 0,
               "report_id" => 25860,
               "formalname" => "FDN Support",
          ),
     );

     $mylistofkeys = array("anc","bmb","cc");
     $mylistofprojects = array(32007,17688,17933,11043,20893,11446,24757,26161,31593,27109,33503,33505,28954,33907); 

     $projects = array();

//list out all the ppl
//Map names to ppl ids
// Amy => 5697, Bob => 10219
// example:
// $mypplnames = array(
//   5697 => "Amy",
//   10219 => "Bob"
// );
include 'ppl.inc';

global $mystring;
$mypplkeys = array_keys($mypplnames);

include 'cred.inc';

foreach ($mypplkeys as &$value) {
    $mystring .= '<user-id type="integer">' . $value . '</user-id>' . PHP_EOL;
}
     // Get the URL of the query, note that we have to run these as background tasks
     $server = $temposlug . 'search';

     // Init cURL
     $ch = curl_init($server);
 
     // Create our query
     $data_string = '<?xml version="1.0" encoding="UTF-8"?>
     <context>
          <interval>alltime</interval>
          <user-ids type="array">
               <!-- explicitly list each user one by one -->
               ' . $mystring . '
          </user-ids>
          <exclude-tags type="array">
               <exclude-tag>INVOICED</exclude-tag>
               <exclude-tag>DO-NOT-INVOICE</exclude-tag>
          </exclude-tags>
          <limit>4000</limit>
     </context>';

     // Set up our cURL options. THESE ARE ALL REQUIRED.
     curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_VERBOSE, true);
     curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
     curl_setopt($ch, CURLOPT_HEADER, 1);
     curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
               'Accept: application/json',
               'Content-Type: application/xml'
         )
     );   

     // Get our response
     $response = curl_exec($ch);

     // Parse out the header
     $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
     $body = substr($response, $header_size);

     // Parse the JSON
     $data = json_decode($body);
     $hours = 0;

     for($i = 0; $i < count($data) - 1; $i++) {
          $match = array_search($data[$i]->project_id, $mylistofprojects);
          if ($match !== FALSE) {
               $whatwegot = $mylistofprojects[$match];
               $thematchedprojectid = $data[$i]->project_id;
               //echo $i . " -> " . $thematchedprojectid . " oh and " . $whatwegot . " and um " . $match . "<br>";
               foreach ($myprojects as $mykey => $value) {
                    //for everything in myprojects
                    //look for a key with $thematchedprojectid
                    if ($value['project_id'] == $thematchedprojectid) {
                      //echo $mykey . '<br>';
                      $myprojects[$mykey]['hours'] += $data[$i]->hours;
                      //echo $myprojects[$mykey]['hours'] . '<br><br>';
                    }
               }
          }
     }



/* here we try to get output on tempo notes for given individuals */
global $wgOut;
          //$wgOut->addHTML("<span>glob</span>");
          //$wgOut->addHTML(print_r($data));
          //$wgOut->addHTML(var_dump($data));
$myppl = array(5697,10219,9438,9182,9437);          
$foundids = array();

$myprj = array();    


// TODO: only show the active clocks, instead of just the latest in time
for($i = 0; $i < count($data)-1; $i++) {
	if( in_array($data[$i]->user_id, $myppl) ) {
		//get the id found
		$pplid = $data[$i]->user_id;
		//do the rest of this only if our current id aint already been found
		if( in_array($data[$i]->user_id, $foundids) ) {
      //do nothing
		} else {

			//$projects[$data[$i]->project_id] += $data[$i]->hours;
			$mytag      = $data[$i]->notes;
               $myprojid = $data[$i]->project_id;

			//$mytag += $data[$i]->hours;
			$updated = $data[$i]->updated_at;
			$isactive = $data[$i]->is_timing;
               if ($isactive) {
                    // get projectid name
                         $apid = $myprojid;
                         $count = 0;
                         $myprojectname = "";
                         foreach ($myprojects as $key => $value) {
                              while (($value['project_id'] == $apid) && ($count!=1)) {
                                   if (isset($value['formalname'])) {
                                        $myprojectname = $value['formalname'];
                                   } else {
                                        $myprojectname = $key;
                                   }
                                   $count = 1;
                              }
                         }

                    $wgOut->addHTML("<span class=\"personhdr\">" . $mypplnames[$pplid] . " (" . $pplid . ")</span>");
                    $wgOut->addHTML("<div class=\"personentry\">");
                    $wgOut->addHTML($myprojectname . " (" . $myprojid . ") <br>");
                    $activeoutput = "<span class=\"green\">clock is active</span>";
                    $foundids[] = $pplid;
                    $wgOut->addHTML($activeoutput . ": ");
                    $wgOut->addHTML($mytag);
                    $wgOut->addHTML("<br>");
                    $wgOut->addHTML($updated);
                    $wgOut->addHTML("");
                    $wgOut->addHTML("</div><!-- .personentry -->");
               } else {
                    //do nothing
                    //$activeoutput = "<span class=\"red\" title=\"typically this is because a older clock has been restarted\">clock ain't active</span>";
               }
		}
	}
}

$wgOut->addHTML("<span class=\"personhdr\">Ppl not found</span>");
$wgOut->addHTML("<div class=\"personentry\">");
$bigarray = $myppl;
$smallarray = $foundids;
$resultarr = array_diff($bigarray, $smallarray);
foreach($resultarr as $key => $value) {
  $wgOut->addHTML($mypplnames[$value] . " | ");
}
$wgOut->addHTML("</div><!-- .personentry -->");


     // Add our CSS
     $wgOut->addHTML('
          <style>
               .left-column {
                    float: left;
                    text-align: center;
                    width: 65px;
               }

               .bar {
                    float: left;
                    font-size: 1.5em;
                    margin: 5px 15px 15px;
                    width: 400px;
                    text-align: center;
                    border: 1px solid #000;
                    height: 25px;
                    position: relative;
               }

               .bar-amount {
                    height: 25px;
                    background-color: #369;
               }

               .hours {
                    position: absolute;
                    top: 5px;
                    width: 400px;
                    text-align: center;
               }

               .thisMonth {
                    font-size: 1.5em;
                    margin-top: 10px;
                    float: left;
                    width: 35px;
                    text-align: center;
               }
          </style>
          ');

     foreach ($myprojects as $key => $value) {
          // $value['hours']
          //echo "<pre>"; print_r($myprojects); echo "</pre>";
          if($key !== false) {
               drawTempoTwoTherms($value['retAmount'], $value['project_id'], $value['hours'], $key);
               //echo "<pre> key is "; echo $key; echo "</pre>";
               //echo "<pre>"; echo $value['retAmount']; echo "</pre>";
               //echo "<pre>"; echo $value['project_id']; echo "</pre>";
               //echo "<pre>"; echo $value['hours']; echo "</pre>";
          }
     }

     //$wgOut->addHTML('<div style="background-color:pink">x</div>');
     $wgOut->addHTML("<h2>Projects</h2>");

     foreach ($myprojects as $key => $value) {
          outputTempoTwoProject($key,$value['project_id'],$value['retLast'],$value['retAmount'],$value['hours'],$value['report_id']);
     }
     $wgOut->addHTML('To change the hours on this report, you need to edit the SpecialTempoTwo.php file.');
}
//end loadTempoTwoData()

function outputTempoTwoProject($key, $project_id, $retLast, $retAmount, $hours, $mtd_rept) {
     global $wgOut;
     include 'cred.inc'; // has the slug we need
     $wgOut->addHTML('<div id="' . $key . '"><div class="left-column"><div class="name"><a href="' . $temposlug . $mtd_rept . '">' . $key . '</a></div><div class="normal">' . $retLast . '</div></div><div class="bar"><div class="hours">' . $hours . '</div><div class="bar-amount" id="' . $key . '-bar"></div></div><div class="thisMonth">' . $retAmount . '</div></div><div style="clear:both"></div>');
}


function drawTempoTwoTherms($retAmount,$project_id,$hours,$name) {
     global $wgOut;

     $wgOut->addHTML('<script>$(document).ready(function() {');
     calcTempoTwoBar($retAmount, $project_id, $hours,$name);
     $wgOut->addHTML('});</script>');
}


function calcTempoTwoBar($retAmount, $project_id, $hours,$name) {
     global $wgOut;
     
     // $hours = $myprojects[$key];
     $perc =  $hours / $retAmount * 100;

     if($perc > 100)
          $perc = 100;

     if($perc > 66) 
          $color = "#900";
     else if ($perc > 33)
          $color = "#990";
     else
          $color = "#447b44";

     $wgOut->addHTML('
          var ' . $name . '_perc = "' . $perc . '%";

          console.log(' . $project_id . ');
          console.log(' . $hours. ');
          console.log(' . $retAmount . ');
          console.log(' . $name . '_perc);
          console.log("--------------------------");

          $("#' . $name . '-bar").width(' . $name . '_perc);
          $("#' . $name . '-bar").css("background-color", "' . $color .'");
     ');
}

/* no comments makes this useless */
function searcharray($value, $key, $array) {
   foreach ($array as $k => $val) {
     if (array_key_exists($key, $val))
          if ($val[$key] == $value) {
                return $k;
          }
   }
   return null;
}