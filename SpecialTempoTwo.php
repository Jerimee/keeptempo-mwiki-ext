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

     // Map project IDs to Project Names
     define("ANC", "17933");
     define("BMB", "11043");
     define("CC", "20893");
     define("ISS", "11446");
     define("MVFR", "24757");
     define("SUPGV", "26161");

     define("AWL", "31593");
     define("VIC", "27109");
     define("DGB", "33503");
     define("HAN", "33505");
     define("PB", "28954");
     define("WCWC", "33907");
     define("TOTAL", "0"); 
     define("FON", "28925");
     define("OBO", "26239");
     define("LAGAY", "29147");
     define("BLD", "33286");
     define("ARC", "33611");
     define("FDN", "32009");
     define("CMOB", "32005");


     $projects = array(
          ANC => 0,
          BMB => 0,
          CC => 0,
          ISS => 0,
          MVFR => 0,
          SUPGV => 0,
          AWL => 0,
          VIC => 0,
          DGB => 0,
          HAN => 0,
          PB => 0,
          WCWC => 0,
          TOTAL => 0,

          FON => 0,
          OBO => 0,
          LAGAY => 0,
          FDN => 0,
          BLD => 0,
          ARC => 0,

          CMOB => 0,
     );

     // Set up retainers
     $retAmount = array(
          // Total 
          TOTAL => 0,

          // Actual adjusted retainer numbers for the current month
          ANC => 35,
          BMB => 10,
          CC  => 55,
          ISS => 60,
          MVFR => 52,
          SUPGV => 45,
          AWL => 17,
          VIC => 29,
          DGB => 16,
          HAN => 10,
          PB => 7,
          WCWC => 10,
          FON => 17,
          OBO => 5, 
          LAGAY => 45.5,
     
          CMOB => 75,
     );

     // 
     $retLast = array(
          TOTAL => 0,

          ANC => 20,
          BMB => 10,
          CC => 45,
          ISS => 50,
          MVFR => 12,
          SUPGV => 50,
          AWL => 20,
          VIC => 10,
          DGB => 16,
          HAN => 10,
          PB => 10,
          WCWC => 10,
          FON => 0,
          OBO => 0,
          LAGAY => 0,
          CMOB => 0,
     );

//list out all the ppl
//Map names to ppl ids
//JERIMEE => 5697,LINDSAY => 10219
// You will need to find the ids of your
// ppl and then put in their names
$mypplnames = array(
5697 => "Joe",
10219 => "Linda",
9438 => "Bob",
9182 => "Nicole",
9437 => "Kate",
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
     // replace STUB with your instance of keeptempo
     $server = 'https://STUB.keeptempo.com/reports/search';

     // Init cURL
     $ch = curl_init($server);
 
     // Create our query
     // you'll need to replace tags with your specific tags
     $data_string = '<?xml version="1.0" encoding="UTF-8"?>
     <context>
          <interval>alltime</interval>
          <bar>foo</bar> <!-- does it ignore gibberish? -->
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

     // Grab the hours from the tempo report
     for($i = 0; $i < count($data)-1; $i++) {

          if( array_key_exists($data[$i]->project_id, $projects) ) {
               $projects[$data[$i]->project_id] += $data[$i]->hours;
               $hours += $data[$i]->hours;
          }
     }

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

     $retLast[TOTAL] = $retLast[ANC] + $retLast[BMB] + $retLast[CC] + $retLast[ISS] + $retLast[MVFR] + $retLast[SUPGV] + $retLast[AWL] + $retLast[VIC] + $retLast[DGB] + $retLast[HAN] + $projects[PB] + $projects[WCWC]; 
     $retAmount[TOTAL] = $retAmount[ANC] + $retAmount[BMB] + $retAmount[CC] + $retAmount[ISS] + $retAmount[MVFR] + $retAmount[SUPGV] + $retAmount[AWL] + $retAmount[VIC] + $retAmount[DGB] + $retAmount[HAN] + $projects[PB] + $projects[WCWC]; 
     $projects[TOTAL] += $projects[ANC] + $projects[BMB] + $projects[CC] + $projects[ISS] + $projects[MVFR] + $projects[SUPGV] + $projects[AWL] + $projects[VIC] + $projects[DGB] + $projects[HAN] + $projects[PB] + $projects[WCWC]; 


     drawTempoTwoThermometers($projects, $retAmount);
     //$wgOut->addHTML('<div style="background-color:pink">x</div>');
     $wgOut->addHTML("<h2>Retainer Clients</h2>");
     //                                                                                             23582 is the standard report URL placeholder
     //           projects   displayname client   id_name    standmnth           this month         report URL 
     outputTempoTwoClient($projects, "ActionNC",     ANC,       "anc",    $retLast[ANC],     $retAmount[ANC],    "23742");
     outputTempoTwoClient($projects, "BMB",     BMB,       "bmb",    $retLast[BMB],     $retAmount[BMB],    "23582");
     outputTempoTwoClient($projects, "CC",      CC,        "cc",     $retLast[CC],      $retAmount[CC],     "23582");
     outputTempoTwoClient($projects, "ISS",     ISS,       "iss",    $retLast[ISS],     $retAmount[ISS],    "26123");
     outputTempoTwoClient($projects, "MVFR",    MVFR,      "mvfr",   $retLast[MVFR],    $retAmount[MVFR],   "23582");
     outputTempoTwoClient($projects, "SUPGV",   SUPGV,     "supgv",  $retLast[SUPGV],   $retAmount[SUPGV],  "23582");
     outputTempoTwoClient($projects, "AWL",     AWL,       "awl",    $retLast[AWL],     $retAmount[AWL],    "33102");
     outputTempoTwoClient($projects, "VIC",     VIC,       "vic",    $retLast[VIC],     $retAmount[VIC],    "23582");
     outputTempoTwoClient($projects, "DGB",     DGB,       "dgb",    $retLast[DGB],     $retAmount[DGB],    "33960");
     outputTempoTwoClient($projects, "HAN",     HAN,       "han",    $retLast[HAN],     $retAmount[HAN],    "33960");
     outputTempoTwoClient($projects, "PB",      PB,        "pb",     $retLast[PB],      $retAmount[PB],     "23582");
     outputTempoTwoClient($projects, "WCWC",    WCWC,      "wcwc",   $retLast[WCWC],    $retAmount[WCWC],   "34655");

     $wgOut->addHTML("<h3>Total for Retainer Clainers</h3>");
     $wgOut->addHTML("<div><em>Left number is for a typical month, and the right number is the actual for this month.</em></div>");

     outputTempoTwoClient($projects, "THEORY",   TOTAL,     "total",  $retLast[TOTAL],   $retAmount[TOTAL],    "23582");


     $wgOut->addHTML("<h2>Pre-pay Clients</h2>");

     outputTempoTwoClient($projects, "FON",          FON,      "fon",    $retLast[FON],      $retAmount[FON],    "28740");
     outputTempoTwoClient($projects, "OBO",          OBO,      "obo",    $retLast[OBO],      $retAmount[OBO],    "31103");
     outputTempoTwoClient($projects, "LAGAY",        LAGAY,    "lagay",  $retLast[LAGAY],    $retAmount[LAGAY],  "23582");

     $wgOut->addHTML("<h2>Other Clients</h2>");

     outputTempoTwoClient($projects, "CMOB",          CMOB,      "cmob",    $retLast[CMOB],      $retAmount[CMOB],    "34205");

     $wgOut->addHTML('To change the hours on this report, you need to edit the SpecialTempoTwo.php file in the extension directory /extensions/Tempo<br>Edit the definition list and two arrays below it');
}
//end loadTempoTwoData()

function outputTempoTwoClient($projects, $display_name, $client, $id_name, $normal, $this_month, $mtd_rept) {
     global $wgOut;

     $wgOut->addHTML('<div id="' . $id_name . '"><div class="left-column"><div class="name"><a href="https://STUB.keeptempo.com/reports/' . $mtd_rept . '">' . $display_name . '</a></div><div class="normal">' . $normal . '</div></div><div class="bar"><div class="hours">' . $projects[$client] . '</div><div class="bar-amount" id="' . $id_name . '-bar"></div></div><div class="thisMonth">' . $this_month . '</div></div><div style="clear:both"></div>');
}


function drawTempoTwoThermometers($projects, $retAmount) {
     global $wgOut;

     $wgOut->addHTML('<script>$(document).ready(function() {');

     calculateTempoTwoBar($projects, $retAmount, ANC,     "anc");
     calculateTempoTwoBar($projects, $retAmount, BMB,     "bmb");
     calculateTempoTwoBar($projects, $retAmount, CC,      "cc");
     calculateTempoTwoBar($projects, $retAmount, ISS,     "iss");
     calculateTempoTwoBar($projects, $retAmount, MVFR,    "mvfr");
     calculateTempoTwoBar($projects, $retAmount, SUPGV,   "supgv");
     calculateTempoTwoBar($projects, $retAmount, AWL,     "awl");
     calculateTempoTwoBar($projects, $retAmount, VIC,     "vic");
     calculateTempoTwoBar($projects, $retAmount, DGB,     "dgb");
     calculateTempoTwoBar($projects, $retAmount, HAN,     "han");
     calculateTempoTwoBar($projects, $retAmount, PB,      "pb");
     calculateTempoTwoBar($projects, $retAmount, WCWC,    "wcwc");

     calculateTempoTwoBar($projects, $retAmount, TOTAL,   "total");

     calculateTempoTwoBar($projects, $retAmount, FON,     "fon");
     calculateTempoTwoBar($projects, $retAmount, OBO,     "obo");
     calculateTempoTwoBar($projects, $retAmount, LAGAY,   "lagay");

     calculateTempoTwoBar($projects, $retAmount, CMOB,    "cmob");

     $wgOut->addHTML('});</script>');
}


function calculateTempoTwoBar($projects, $retAmount, $client, $id_name) {
     global $wgOut;

     $perc = $projects[$client] / $retAmount[$client] * 100;

     if($perc > 100)
          $perc = 100;

     if($perc > 66) 
          $color = "#900";
     else if ($perc > 33)
          $color = "#990";
     else
          $color = "#447b44";

     $wgOut->addHTML('
          var ' . $id_name . '_perc = "' . $perc . '%";

          console.log(' . $client . ');
          console.log(' . $projects[$client] . ');
          console.log(' . $retAmount[$client] . ');
          console.log(' . $id_name . '_perc);
          console.log("--------------------------");

          $("#' . $id_name . '-bar").width(' . $id_name . '_perc);
          $("#' . $id_name . '-bar").css("background-color", "' . $color .'");
     ');
}