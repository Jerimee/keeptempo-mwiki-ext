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

     include 'projects.inc';
     $projects = array();

     //list out all the ppl
     // example:
     // $mypplnames = array(
     //   5697 => "Amy",
     //   10219 => "Bob",
     //   9182 => "Nicole"
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
          $match = array_search($data[$i]->project_id, $myListOfProjects);
          if ($match !== FALSE) {
               $whatwegot = $myListOfProjects[$match];
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

     global $wgOut;

     // css
     $styleurl = "/extensions/Tempotwo/modules/tempostyles.css";
     $wgOut->addHTML('<link type="text/css" rel="stylesheet" media="all" href="' . $styleurl . '" /><!-- dumb -->');
     // draw teh thermometers
     foreach ($myprojects as $key => $value) {
          if($key !== false) {
               drawTempotwoTherms($value['retAmount'], $value['project_id'], $value['hours'], $key,$value['nicolePortion']);
          }
     }

     //$wgOut->addHTML('<div style="background-color:pink">x</div>');
     $wgOut->addHTML("<h2>Projects</h2>");

     // foreach ($myprojects as $key => $value) {
     foreach ($mylistofkeys as $value) {
          // outputTempoTwoProject($key,$value['project_id'],$value['retLast'],$value['retAmount'],$value['hours'],$value['report_id'],$value['month']);
          outputTempoTwoProject(
               $value,
               $myprojects[$value]['project_id'],
               $myprojects[$value]['retLast'],
               $myprojects[$value]['retAmount'],
               $myprojects[$value]['hours'],
               $myprojects[$value]['report_id'],
               $myprojects[$value]['month'],
               $myprojects[$value]['formalname'],
               $myprojects[$value]['nicolePortion']
          );
     }

     $wgOut->addHTML("<h2>Hourly Projects</h2>");

     foreach ($hourlylist as $value) {
          // outputTempoTwoProject($key,$value['project_id'],$value['retLast'],$value['retAmount'],$value['hours'],$value['report_id'],$value['month']);
          outputTempoTwoProject(
               $value,
               $myprojects[$value]['project_id'],
               $myprojects[$value]['retLast'],
               $myprojects[$value]['retAmount'],
               $myprojects[$value]['hours'],
               $myprojects[$value]['report_id'],
               $myprojects[$value]['month'],
               $myprojects[$value]['formalname'],
               0
          );
     }

     $wgOut->addHTML('To change the hours on this report, you need to edit the SpecialTempoTwo.php file.');


     $wgOut->addHTML("<h2>People</h2>");
     /* here we try to get output on tempo notes for given individuals */
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
     $wgOut->addHTML("<h2>Note</h2>");
     $wgOut->addHTML('To change the hours on this report, you need to edit the SpecialTempotwo.php file.');
     $wgOut->addHTML("<h2>Nicole</h2>");
     foreach ($mylistofkeys as $value) {
          switch($value){
            case 'cc':
               $nicole = $myprojects[$value]['nicolePortion'];
               if ($nicole>0) {
                    $wgOut->addHTML("<h3>Courage</h3>");
                    $wgOut->addHTML("<p>Nicole should not have more than " . $nicole . ".</p>");
                    $nicolex = 0;
                    $wgOut->addHTML("<p>Nicole has " . $nicolex . ".</p>");
                    $justNicoleHours = $myprojects[$value]['hours'];
                    outputTempotwoProjectIndividual(
                         $value,
                         $myprojects[$value]['project_id'],
                         $myprojects[$value]['nicolePortion'],
                         $justNicoleHours,
                         $myprojects[$value]['month'],
                         $myprojects[$value]['formalname']
                    );
               } else {
                    //do nothing
               }
               break; // shojuld this be inside the if?
            case 'fon':
                $wgOut->addHTML("<h3>FON</h3>");
                break;
          }
     }
}
//end loadTempoTwoData()

function outputTempoTwoProject($key, $project_id, $retLast, $retAmount, $hours, $mtd_rept, $month, $longname,$nPortion) {
     global $wgOut;
     include 'cred.inc'; // has the slug we need
     if ($nPortion > 0) {
          $nChunk = '<div id="nportion">N' . $nPortion . '</div>';
     } else {
          $nChunk = "<!-- no nPortion -->";
     }

     $month = '<span id="whatmonth">(' . strtolower($month) . ')</span>';
     $wgOut->addHTML('<div id="' . $key . '">
                         <div class="left-column">
                              <div class="name">
                                   <a href="' . $temposlug . $mtd_rept . '" title="' . $longname . '">' . $key . '</a> 
                              </div>
                              <div class="normal">' . $retLast . '</div>
                         </div>
                         <div class="bar">
                              <div class="hours">' . $hours . ' ' . $month . '</div>
                              <div class="bar-amount" id="' . $key . '-bar"></div>
                         </div>
                         <div class="thisMonth">' . $retAmount . '</div>
                    </div> <!-- /' . $key . ' -->' . 
                    $nChunk . 
                    '<div style="clear:both"></div>');
}

function outputTempotwoProjectIndividual($key, $project_id, $retAmount, $hours, $month, $longname) {
     global $wgOut;
     include 'cred.inc'; // has the slug we need

     $month = '<span id="whatmonth">(' . strtolower($month) . ')</span>';
     $wgOut->addHTML(
          '<div id="' . $key . '">
                <div class="left-column">
                    <div class="name">
                         <span>' . $key . '</span> 
                    </div>
               </div>
               <div class="bar">
                    <div class="hours">' . $hours . ' ' . $month . '</div>
                    <div class="bar-amount" id="' . $key . '-bar"></div>
               </div>
               <div class="thisMonth">' . $retAmount . '</div>
          </div> <!-- /' . $key . ' -->' . 
          '<div style="clear:both"></div>'
     );
}

/* drawTempotwoTherms() */
function drawTempotwoTherms($retAmount,$project_id,$hours,$name,$nPortion) {
     global $wgOut;

     $wgOut->addHTML('<script>$(document).ready(function() {');
     calcTempotwoBar($retAmount, $project_id, $hours,$name,$nPortion);
     $wgOut->addHTML('});</script>');
}

/* calcTempotwoBar() */
function calcTempotwoBar($retAmount, $project_id, $hours,$name,$nPortion) {
     global $wgOut;
     
     // $hours = $myprojects[$key];
     $perc =  $hours / $retAmount * 100;

     if($perc > 100)
          $perc = 100;

     if($perc > 66) {
          $color = "#900";
     } else if ($perc > 33) {
          if ($nPortion > 0) {
               $color = "#996B00";
          } else {
               $color = "#990";
          }
          
     } else {
          if ($nPortion > 0) {
               $color = "#58CE58";
          } else {
               $color = "#447b44";  
          }
     }

    

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