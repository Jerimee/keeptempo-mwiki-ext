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
     );

$mylistofprojects = array(17933,11043,20893,11446,24757,26161,31593,27109,33503,33505,28954,33907); 
     // Map project IDs to Project Names

     //define("anc", "17933");
     //define("bmb", "11043");
     //define("cc", "20893");
     //define("iss", "11446");
     //define("mvfr", "24757");
     //define("supgv", "26161");
     //define("awl", "31593");
     //define("vic", "27109");
     //define("dgb", "33503");
     //define("han", "33505");
     //define("pb", "28954");
     //define("wcwc", "33907");
     //foreach ($myprojects as $key => $value) {
     //     $keyString = (string)$key;
     //     define($keyString, $value['project_id']);
     //     echo "key =" . $key . "<br>";
     //     echo "pid =" .  $value['project_id'] . "<br>";
     //}

     $projects = array();

//list out all the ppl
//Map names to ppl ids
//JERIMEE => 5697,LINDSAY => 10219
$mypplnames = array(
5697 => "Jerimee",
10219 => "Lindsay",
9438 => "Blake",
9182 => "Nicole",
9437 => "Katie",
9083 => "unknown",
6651 => "unknown",
9080 => "unknown",
9098 => "unknown",
7767 => "unknown",
9310 => "unknown",
9365 => "unknown",
8263 => "unknown",
8742 => "unknown",
8809 => "uh",
9068 => "unknown",
9317 => "unknown",
6657 => "unknown",
9400 => "unknown",
7765 => "unknown",
10269 => "whoisit",
);

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

      //echo "<pre>"; print_r($myprojects); echo "</pre>";


/* here we try to get output on tempo notes for given individuals */
global $wgOut;
          //$wgOut->addHTML("<span>glob</span>");
          //$wgOut->addHTML(print_r($data));
          //$wgOut->addHTML(var_dump($data));
$myppl = array(5697,10219,9438,9182,9437);          
$foundids = array();

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
			$mytag = $data[$i]->notes;
			//$mytag += $data[$i]->hours;
			$updated = $data[$i]->updated_at;
			$isactive = $data[$i]->is_timing;
               if ($isactive) {
                    $wgOut->addHTML("<span class=\"personhdr\">" . $mypplnames[$pplid] . " (" . $pplid . ")</span>");
                    $wgOut->addHTML("<div class=\"personentry\">");
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


			//add it to a list of ids found because
			//as soon as I find a person id I want to stop looking
			//for instances of that id and move on to the next id
			//$foundids[] = $pplid;
			//$wgOut->addHTML("<pre>" . $mystring);
			//$wgOut->addHTML(implode(",",$foundids));
			//$wgOut->addHTML("</pre><br>");
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
     //                                                                                             23582 is the standard report URL placeholder
     //           projects   displayname client   id_name    standmnth           this month         report URL 
     //outputTempoTwoClient($projects, "ActionNC",     ANC,       "anc",    $retLast[ANC],     $retAmount[ANC],    "23742");
     //outputTempoTwoClient($projects, "BMB",     BMB,       "bmb",    $retLast[BMB],     $retAmount[BMB],    "23582");
     //outputTempoTwoClient($projects, "CC",      CC,        "cc",     $retLast[CC],      $retAmount[CC],     "23582");
     //outputTempoTwoClient($projects, "ISS",     ISS,       "iss",    $retLast[ISS],     $retAmount[ISS],    "26123");
     //outputTempoTwoClient($projects, "MVFR",    MVFR,      "mvfr",   $retLast[MVFR],    $retAmount[MVFR],   "23582");
     //outputTempoTwoClient($projects, "SUPGV",   SUPGV,     "supgv",  $retLast[SUPGV],   $retAmount[SUPGV],  "23582");
     //outputTempoTwoClient($projects, "AWL",     AWL,       "awl",    $retLast[AWL],     $retAmount[AWL],    "33102");
     //outputTempoTwoClient($projects, "VIC",     VIC,       "vic",    $retLast[VIC],     $retAmount[VIC],    "23582");
     //outputTempoTwoClient($projects, "DGB",     DGB,       "dgb",    $retLast[DGB],     $retAmount[DGB],    "33960");
     //outputTempoTwoClient($projects, "HAN",     HAN,       "han",    $retLast[HAN],     $retAmount[HAN],    "33960");
     //outputTempoTwoClient($projects, "PB",      PB,        "pb",     $retLast[PB],      $retAmount[PB],     "23582");
     //outputTempoTwoClient($projects, "WCWC",    WCWC,      "wcwc",   $retLast[WCWC],    $retAmount[WCWC],   "34655");
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