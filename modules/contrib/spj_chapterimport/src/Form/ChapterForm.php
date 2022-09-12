<?php
/**
 * @file
 * Contains \Drupal\spj_chapterimport\Form\ChapterForm.
 */
namespace Drupal\spj_chapterimport\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;

class ChapterForm extends FormBase {
    
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'spj_chapter_form';
    }

    private function getChapterType($title){

        if(strpos($title, "Pro")){
            return 61;
        } else {
            return 62;
        }
        
    }

    private function checkIfNodeExists($title, $data){
    
        if($title){
            $nodes = \Drupal::entityTypeManager()
                ->getStorage('node')
                ->loadByProperties([
                    'title' => $title,
                ]);
            if(count($nodes)>0){

                // update here
                // get the taxonomy for the state, then, set region
                $stateRegionID = $this->stateToRegion($data['statename']);

                foreach($nodes as $nod){

                    $nod->field_region = array(
                        'target_id' => $stateRegionID
                    );
                    $nod->save();
                }
                
                return $title . " exists. updating region: ". $stateRegionID . "<br />";


            } else {
                $newNid =  $this->createNode($title,$data);
                return "[create '" . $title . "']";
            }
        }
    }


    private function getOrMakeTerm($term, $vocab){

        //dpm($term);

        $properties['name'] = $term;
        $properties['vid'] = $vocab;
    
        $termload = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $term, 'vid' => $vocab]);
        $tid=1;
        //dpm($termload);
        if($termload!=null){
            foreach($termload as $ter){
                
                $tid = $ter->get('tid')->value;
                //dpm($tid);
            }
            return $tid;
        } else {

            if($term!=null && $term!=""){
            $new_term = Term::create([
                'vid' => $vocab,
                'name' => $term,
            ]);

            
            $new_term->enforceIsNew();
            $new_term->save();
            return $new_term->id();
            } else {
                return 0;
            }

            }
    }

    private function createNode($title,$data){

        $machineType = "chapter";

        $retId = "";
        $dataArray = null;
      
        $memTypes = $data['memcodearr'];
        $memTypeArr = array();
        foreach($memTypes as $mem){
            $memTypeArr[] =array("target_id"=> $mem);

        } 


            $dataArray = array(
                'type' => $machineType,
                'title' => trim($title),
                'langcode' => 'en',
                 //'uid' => $node->post_id,
                'status' => 1,
                'field_state' => array(
                    'target_id' => $data['state']
                ),
                'field_chapter_type' => array(
                    'target_id' => $data['chaptertype']
                ),
                'field_memcodearr' => $memTypeArr,
                'field_address' => trim($data['address']),
                'field_address_' => trim($data['city']),
                'field_address_zip_code' => trim($data['zip']),
                'field_chapter_email' => trim($data['email']),
                'body' => array(
                    'value' => '',
                    'format' => 'full_html',
                )
            );

            //dpm($dataArray);


            $node = Node::create($dataArray);
            $node->save();
            $nid = $node->id();
            $retId = $nid;

            return $retId;

      
    }

    private function stateToRegion($stat){

        $regionID = array(
            "1"=>"49",
            "2"=>"50",
            "3"=>"51",
            "4"=>"52",
            "5"=>"53",
            "6"=>"54",
            "7"=>"55",
            "8"=>"56",
            "9"=>"57",
            "10"=>"58",
            "11"=>"59",
            "12"=>"60",

        );

        $thisState = $this->stateToAbbrev($stat);

        $regionStates = array(
            "1" => array(
                "Connecticut",
                "Maine",
                "Massachusetts",
                "New Hampshire",
                "New Jersey",
                "New York",
                "Pennsylvania",
                "Rhode Island",
                "Vermont",
            ),
            "2" => array(
                "Delaware",
                "District of Columbia",
                "Maryland",
                "North Carolina",
                "Virginia",
            ),
            "3" => array(
                "Alabama",
                "Florida",
                "Georgia",
                "South Carolina",
                "Puerto Rico",
                "U.S. Virgin Islands",
                "Guam",
                "Micronesia"
            ),
            "4" => array(
                "Michigan",
                "Ohio",
                "Western Pennsylvania",
                "West Virginia",
            ),
            "5" => array(
                "Illinois",
                "Indiana",
                "Kentucky",
            ),
            "6" => array(
                "Minnesota",
                "North Dakota",
                "South Dakota",
                "Wisconsin",
            ),
            "7" => array(
                "Iowa",
                "Kansas",
                "Missouri",
                "Nebraska",
            ),
            "8" => array(
                "Texas", 
                "Oklahoma",
            ),
            "9" => array(
                "Colorado",
                "New Mexico",
                "Utah",
                "Wyoming",
            ),
            "10" => array(
                "Alaska",
                "Idaho",
                "Montana",
                "Oregon",
                "Washington",
            ),
            "11" => array(
                "Arizona",
                "California",
                "Guam",
                "Hawaii",
                "Nevada",
                "Mariana Islands",
            ),
            "12" => array(
                "Arkansas",
                "Louisiana",
                "Mississippi",
                "Tennessee",
            ),
        );

        $region = "Region 1";
        foreach($regionStates as $reg=>$states){

            foreach($states as $st){
                
               if( $thisState == $st){
                $region = $reg;
                //dpm( $stat . ":" . $thisState . ":" . $st);
               }
            }
        }

        
        
        $regionNum=1;
        if($regionID[$region]){
            $regionNum = $regionID[$region];
        }

        
        return $regionNum;
    }

    private function stateToAbbrev($state){

        $list = array(
            "Alabama"=>"AL",
            "Alaska"=>"AK",
            "Arizona"=>"AZ",
            "Arkansas"=>"AR",
            "California"=>"CA",
            "Colorado"=>"CO",
            "Connecticut"=>"CT",
            "Delaware"=>"DE",
            "Florida"=>"FL",
            "Georgia"=>"GA",
            "Hawaii"=>"HI",
            "Idaho"=>"ID",
            "Illinois"=>"IL",
            "Indiana"=>"IN",
            "Iowa"=>"IA",
            "Kansas"=>"KS",
            "Kentucky"=>"KY",
            "Louisiana"=>"LA",
            "Maine"=>"ME",
            "Maryland"=>"MD",
            "Massachusetts"=>"MA",
            "Michigan"=>"MI",
            "Minnesota"=>"MN",
            "Mississippi"=>"MS",
            "Missouri"=>"MO",
            "Montana"=>"MT",
            "Nebraska"=>"NE",
            "Nevada"=>"NV",
            "New Hampshire"=>"NH",
            "New Jersey"=>"NJ",
            "New Mexico"=>"NM",
            "New York"=>"NY",
            "North Carolina"=>"NC",
            "North Dakota"=>"ND",
            "Ohio"=>"OH",
            "Oklahoma"=>"OK",
            "Oregon"=>"OR",
            "Pennsylvania"=>"PA",
            "Rhode Island"=>"RI",
            "South Carolina"=>"SC",
            "South Dakota"=>"SD",
            "Tennessee"=>"TN",
            "Texas"=>"TX",
            "Utah"=>"UT",
            "Vermont"=>"VT",
            "Virginia"=>"VA",
            "Washington"=>"WA",
            "West Virginia"=>"WV",
            "Wisconsin"=>"WI",
            "Wyoming"=>"WY",
            "Puerto Rico"=>"PR",
            "Washington D.C."=>"DC",
            "Guam"=>"GU",
            "Micronesia"=>"FM"
        );
        $res ="";
        foreach($list as $key=>$st){
            if($state==$st){
                $res= $key;
            }
        }

        return $res;
    }


    public function buildForm(array $form, FormStateInterface $form_state) {


        $form['entry_desc'] = array (
            '#type' => 'textarea',
            '#title' => t('One Sentence Description'),
            '#required' => FALSE,
          );

            $form['file_upload_details'] = array(
            '#markup' => t('<b>The File</b>'),
            );
            
            $validators = array(
            'file_validate_extensions' => array('csv'),
            );
            $form['my_file'] = array(
            '#type' => 'managed_file',
            '#name' => 'my_file',
            '#title' => t('File *'),
            '#size' => 20,
            '#description' => t('CSV format only'),
            '#upload_validators' => $validators,
            '#upload_location' => 'public://chapterimports/',
            );

            $form['actions']['#type'] = 'actions';
            $form['actions']['submit'] = array(
              '#type' => 'submit',
              '#value' => $this->t('Submit'),
              '#button_type' => 'primary',
            );
            return $form;
        }


        public function submitForm(array &$form, FormStateInterface $form_state) {
            \Drupal::messenger()->addMessage(t("Chapter Import Done!! Registered Values are:"));
              foreach ($form_state->getValues() as $key => $value) {
                //\Drupal::messenger()->addMessage($key . ': ' . $value);
                }
                $file = \Drupal::entityTypeManager()->getStorage('file')
                ->load($form_state->getValue('my_file')[0]); 
                // Just FYI. The file id will be stored as an array
                // And you can access every field you need via standard method
                
                $filename = "./sites/default/files/chapterimports/" . $file->get('filename')->value;
                $dat = file_get_contents($filename);
                
                
                $resArray = [];
                $lines=explode("\n",$dat);

                /*
                0 Name,
                1 Address Line1,
                2 Address Line2,
                3 Address City,
                4 Address State,
                5 Address Zip,
                6 Email Address,
                7 Membership Name,
                8 Membership Code
                */
                $counter = 0;
                foreach($lines as $line){
                    
                    if($counter!==0){
                    $parts = explode(",", $line);
                    if(count($parts)>7){
                        $termID = $this->getOrMakeTerm($this->stateToAbbrev($parts[4]), "us_state");
                        
                        $temp = array();
                        if($parts[0]!=""){
                            $temp['name'] = $parts[0];
                            $temp['address1'] = $parts[1];
                            $temp['address2'] = $parts[2];
                            $temp['city'] = $parts[3];
                            $temp['state'] = $termID ;
                            $temp['stateName'] = $parts[4];
                            $temp['zip'] = $parts[5];

                            $temp['email'] = $parts[6];
                            $temp['memname'] = $parts[7];
                            $temp['memcode'] = $parts[8];


                            $resArray[$parts[0]][] = $temp;
                        }
                    }
                    
                    }
                    $counter++;
                    //dpm($line);

                   
                }
                
                $master = [];
                foreach($resArray as $name=>$arr){
                    //$master[$name] = array();

                    
                    $address = "";
                    $city = "";
                    $zip = "";
                    $email = "";
                    $state = "";
                    $statename = "";
                    
                    $memCodes = array();
                    foreach($arr as $loops){
                        $address = $loops['address1'] . " " . $loops['address1'];
                        $city = $loops['city'];
                        $zip = $loops['zip'];
                        $email = $loops['email'];
                        $state = $loops['state'];
                        $statename = $loops['stateName'];

                        $memCodes[] = $loops['memcode'];

                    }

                    $compiled = array(
                        "name" => $name,
                        'address' => $address,
                        'city'=>$city,
                        'zip'=>$zip,
                        'state' => $state,
                        'statename'=> $statename,
                        'chaptertype'=> $this->getChapterType($name),
                        'email'=>$email,
                        'memcodes'=>$memCodes
                    );
                    $master[$name] = $compiled;



                }
                //dpm($master);


                foreach($master as $key=>$entry){

                    $memTerms = array();
                    foreach($entry['memcodes'] as $code){
                        $termy = $this->getOrMakeTerm($code, "chapter_memberships");
                        //dpm($termy);
                        $memTerms[] = $termy;
                    }
                    $entry['memcodearr'] = $memTerms;
                    $ret = $this->checkIfNodeExists($key, $entry);
                    dpm($ret);
                }

          }

        
}
