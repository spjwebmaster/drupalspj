<?php 
namespace Drupal\spj_customimport\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use \Drupal\Core\File\FileSystemInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

class CustomImportController extends ControllerBase {



    public function scrapePage($url){

        $page = file_get_contents($url);
        return $page;

    }
    private function prepareScrape($type){
        $mark = '<br /><a href="/customimport/' . $type . '" data-type="' .$type .'" class="loadScrape btn btn-primary">Load ' . $type . '</a><br />';
        return $mark;
    }
    public function index($type){

    }

    private function checkIfNodeExists($data, $type){
        $title = $data->title;
        if($title){
            $nodes = \Drupal::entityTypeManager()
                ->getStorage('node')
                ->loadByProperties([
            'title' => $title,
            ]);
            if(count($nodes)>0){
                return $title . " exists. Skipping<br />";
            } else {
                $newNid =  $this->createNode($data,$type);
                return "[going to create '" . $title . "' in the future]" .  $newNid. "<br />";
            }
        }
    }

    private function getOrMakeTerm($term, $vocab){

        $properties['name'] = $term;
        $properties['vid'] = $vocab;

        $terms = \Drupal::entityManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties($properties);
        $term = reset($terms);
    
        $tid=( !empty($term) ? $term->id() : 0);

        if($tid!=0){
        } else {

        $new_term = Term::create([
            'vid' => $vocab,
            'name' => $term,
          ]);
          
          $new_term->enforceIsNew();
          $new_term->save();
          return $new_term->id();
        }
    }


    private function uploadMedia($url){

        $file_data = file_get_contents($url);
        $file_namefull = explode("/",$url);
        $file_name = $file_namefull[count($file_namefull)-1];
        $file_name = str_replace('%20', '_', $file_name);
        
        $file = file_save_data($file_data, 'public://' . $file_name, FileSystemInterface::EXISTS_REPLACE);
        

        $media = Media::create([
            'bundle'=> 'image',
            'uid' => \Drupal::currentUser()->id(),
            "thumbnail" => [
                "target_id" => $file->id(),
                "alt" => $file->getFilename(),
           ],
           "field_media_image" => [
               "target_id" => $file->id(),
                "alt" => $file->getFilename(),
           ],
        ]);

        $media->setName($file_name)
        ->setPublished(TRUE)
        ->save();
        $mediaId = $media->get('mid')->value;

        //$mediaId = $file->id();
        //dpm($media);
        return $mediaId;

    }

    private function createNode($data, $type){

        $machineType = $type;

        $retId = "";
        $dataArray = null;
        if($type=="hq"||$type=="stc"||$type=="board"||$type=="rc" || $type=="foundation" || $type == "whistle"){
            $machineType = "bio";

            $roleId = 12;
            switch($type) {
                case "hq":  $roleId= 12; break;
                case "stc": $roleId= 21; break;
                case "board": $roleId= 6; break;
                case "rc": $roleId= 5; break;
                case "foundation": $roleId= 172; break;
                case "whistle": $roleId= 249; break;
            }
            

            $title = trim($data->title);

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                 //'uid' => $node->post_id,
                'status' => 1,
                'field_role' => array(
                    'target_id' => $roleId
                ),
                'field_twitter' => trim($data->field_twitter),
                'field_email' => trim($data->field_email),
                'field_title' => trim($data->field_title),
                'body' => array(
                    'value' => $data->body,
                    'format' => 'full_html',
                )
            );


            $node = Node::create($dataArray);
            if($data->field_profile_image){

                $mediaId = CustomImportController::uploadMedia($data->field_profile_image);
                $node->field_profile->target_id = $mediaId;
            }
            
            

            $node->save();
            $nid = $node->id();
            $retId = $nid;

        } else if($type=="ldf"){
            $machineType = "ldf_entry";
            $title = trim($data->title);

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                'status' => 1,
                'field_published_date' => $data->pubDate,
                'body' => array(
                    'value' => $data->body,
                    'format' => 'full_html',
                )
            );

            //dpm($dataArray);
            $node = Node::create($dataArray);
            $node->save();
            $nid = $node->id();
            $retId = $nid;
        
        } else if($type=="leads"){
            $machineType = "leads_issue";
            $title = trim($data['title']);

            $timestamp = strtotime($data['pubDate']);
            $activeDate = date('Y-m-d', $timestamp);

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                'status' => 1,
                'field_active_date' => $activeDate,
                'field_leads_link' => $data['description'],
                'body' => array(
                    'value' => $data['description'],
                    'format' => 'full_html',
                )
            );

            //dpm($dataArray);
            $node = Node::create($dataArray);
            $node->save();
            $nid = $node->id();
            $retId = $nid;

        } else if($type=="newsawards"){
            $machineType = "news_item";
            $title = trim($data['title']);

            $timestamp = strtotime($data['pubDate']);
            $activeDate = date('Y-m-d', $timestamp);

            $newBody = "";
            $legacyContact = "";
            $pieces = explode("<br>", $data['description']);
           
            $canExtract = false;
            $maxLoop = 0;
            $counter = 0;
            foreach($pieces as $val){
                //dpm("<textarea>" . $val . "</textarea>");
                if($val ==""){
                   $maxLoop = $counter;
                   break;
                } else {
                    $counter++;
                }
            }
            //dpm($maxLoop);
            $counter = 0;
            foreach($pieces as $in => $val){
                if($counter<$maxLoop){
                    $legacyContact .= "<div class='contact'>" . $val . "</div>";
                } else {
                    $newBody .= $val . "<br />";
                }
                $counter++;

            }

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                 //'uid' => $node->post_id,
                'status' => 1,
                'field_active_date' => $activeDate,
                'field_legacy_contact' => '',
                'body' => array(
                    'value' => $data['description'],
                    'format' => 'full_html',
                )
            );

            //dpm($dataArray);
            $node = Node::create($dataArray);
            $node->save();
            $nid = $node->id();
            $retId = $nid;
            dpm($retId);
        }  else if($type=="newsinet"){
            $machineType = "news_item";
            $title = trim($data['title']);

            $timestamp = strtotime($data['pubDate']);
            $activeDate = date('Y-m-d', $timestamp);

            $newBody = "";
            $legacyContact = "";
            $pieces = explode("<br>", $data['description']);
           
            $canExtract = false;
            $maxLoop = 0;
            $counter = 0;

            $field_legacy_uri = trim($data['link']);
         

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                 //'uid' => $node->post_id,
                'status' => 1,
                'field_legacy_uri'=> $field_legacy_uri,
                'field_active_date' => $activeDate,
                'body' => array(
                    'value' => $data['description'],
                    'format' => 'full_html',
                )
            );

            //dpm($dataArray);
            /*
            $node = Node::create($dataArray);
            $node->save();
            $nid = $node->id();
            $retId = $nid;
            dpm($retId);
            */
        }
        else if($type=="inetdiff"){
            $machineType = "news_item";
            $title = trim($data['title']);

            $timestamp = strtotime($data['pubDate']);
            $activeDate = date('Y-m-d', $timestamp);

            $newBody = "";
            $legacyContact = "";
            $pieces = explode("<br>", $data['description']);
        
            $canExtract = false;
            $maxLoop = 0;
            $counter = 0;

            $field_legacy_uri = trim($data['link']);
        

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                //'uid' => $node->post_id,
                'status' => 1,
                'field_legacy_uri'=> $field_legacy_uri,
                'field_active_date' => $activeDate,
                'body' => array(
                    'value' => $data['description'],
                    'format' => 'full_html',
                )
            );

            dpm($dataArray);
            //$node = Node::create($dataArray);
            //$node->save();
            //$nid = $node->id();
            //$retId = $nid;
            //dpm($retId);
        } else if($type=="news"){
            $machineType = "news_item";
            $title = trim($data['title']);

            $timestamp = strtotime($data['pubDate']);
            $activeDate = date('Y-m-d', $timestamp);

            $newBody = "";
            $legacyContact = "";
            $pieces = explode("<br>", $data['description']);
            $field_legacy_uri = trim($data['link']);

           
            $canExtract = false;
            $maxLoop = 0;
            $counter = 0;
            foreach($pieces as $val){
                //dpm("<textarea>" . $val . "</textarea>");
                if($val ==""){
                   $maxLoop = $counter;
                   break;
                } else {
                    $counter++;
                }
            }
            //dpm($maxLoop);
            $counter = 0;
            foreach($pieces as $in => $val){
                if($counter<$maxLoop){
                    $legacyContact .= "<div class='contact'>" . $val . "</div>";
                } else {
                    $newBody .= $val . "<br />";
                }
                $counter++;

            }

            
            
            
            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                 //'uid' => $node->post_id,
                'status' => 1,
                'field_active_date' => $activeDate,
                'field_legacy_uri' => $field_legacy_uri,
                'field_legacy_contact' => '',
                'body' => array(
                    'value' => $data['description'],
                    'format' => 'full_html',
                )
            );

            //dpm($dataArray);
            $node = Node::create($dataArray);
            $node->save();
            $nid = $node->id();
            $retId = $nid;
            dpm($retId);
        }



       
        
        return $retId;
       
    }

    private function extractContactFromBody($body){

        // extract from string here
        $retArr = array(
            "body" => $body,
            "contacts"=> array("1","2")
        );

        return $retArr;
    }

    private function loadRSS($url, $type){
        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $send = "going to load ". $url . " | " . $type . " | " .$language . "<br /><br />";
        $xml = simplexml_load_file($url);
        foreach($xml->channel->item as $item){

            $title = trim($item->title);
            $link = $item->link;
            $pub = $item->pubDate;            
            $desc = $item->description;
            
            
            $send .= $title . "<br />";
            $send .= $pub . " | ";
            $send .= $link . "<br />";

            $bodyArr = CustomImportController::extractContactFromBody($desc);

            $dataAr = array(
                "title" => trim($title),
                "description" => trim($desc),
                "pubDate"=> trim($pub),
                "link"=>trim($link)
                
            );


            if($title!=null && $title!=""){
                // search for a contact with that name from the body!
                /*
                $nodes = \Drupal::entityTypeManager()
                            ->getStorage('node')
                            ->loadByProperties([
                        'title' => $title,
                        ]);
                        */


                        $nodesAll = \Drupal::entityTypeManager()
                            ->getStorage('node')->getQuery();

                                //$nodes->condition('type', 'news_item');
                                //$nodes->condition('field_legacy_uri', );
                                //$nodesAll->condition('title', '%' . $title . '%', "like");
                                $nodesAll->condition('title', $title);
                                $nodes = $nodesAll->execute();

                        // do a like comparison instead

                $send .= "<strong>Found " . count($nodes) . " nodes with this title</strong>";
                if(count($nodes) >0){
                    if($type=="newsinet"){
                        $send .= " Need to update and add the REF";
                        if(count($nodes) ==1){
                            
                            foreach($nodes as $nid){
                                //dpm($nid);
                                
                                $tnode = Node::load($nid);
                                $tnode->field_legacy_uri = $link;
                                //$tnode->save();
                                
                                
                            }
                           
                            
                           
                        }
                    } else {
                        $send .= " SKIPPING";
                    }
                   
                } else {
                    $send .= " Create! ";
                    $newNid =  $this->createNode($dataAr,$type);
                    $send .= $newNid;
                }
            }
            $send .="<hr />";
        }
        return $send;
    }


    public function import($type){

        $ret = "";
        
        $urlToload = "";
        $isScrape = false;
        $showMenu = false;
        $showSubmit = true;
        switch($type){
            case "news": $urlToload = "https://spj.org/rss_news.rss?T=S"; break;
            case "newsawards": $urlToload = "https://www.spj.org/rss_news.asp?T=A"; break;
            case "newsinet": $urlToload = "https://www.spj.org/newsImportXml.xml"; break;
            case "inetdiff":  $isScrape = true; $urlToload = "https://www.spj.org/inetdups.csv"; break;
            case "leads": $urlToload = "https://spj.org/rss_spjleads.asp?T=S"; break;
            case "ldf": $isScrape = true; $showSubmit=false; $urlToload = "https://www.spj.org/ldf-a.asp"; break;
            case "calendar": $urlToload = "http://calendar.spjnetwork.org/feed.php?ex="; break;
            case "hq": $isScrape = true; $showSubmit=false; $urlToload = "https://spj.org/hq.asp"; break;
            case "stc": $isScrape = true; $showSubmit=false; $urlToload = "https://spj.org/stc.asp"; break;
            case "rc": $isScrape = true; $showSubmit=false; $urlToload = "https://spj.org/regional-coordinators.asp"; break;
            case "board": $isScrape = true; $showSubmit=false; $urlToload = "https://spj.org/spjboard.asp"; break;
            case "foundation": $isScrape = true; $showSubmit=false; $urlToload = "https://www.spj.org/foundation-board.asp"; break;
            case "whistle": $isScrape = true; $showSubmit=false; $urlToload = "https://www.spj.org/whistleblower/credits.asp"; break;
            case "foi": $isScrape = true; $showSubmit=false; $urlToload = "https://www.spj.org/findfoi.asp"; break;
            case "choose"; $showMenu = true; $showSubmit=false; break;
            //case "fetchnews"; $showMenu = true; $isScrape=true;  break;
        }

        $backLink = "";
        if($type!=="choose"){
            $backLink = "&nbsp;<small><a href='/customimport/'>&laquo;back</a></small><br />";
        }



        if($type=="fetchnews"){

            if(isset($_REQUEST['post'])){
                //
                $ret = "updating";
                if(isset($_REQUEST['body'])){
                    $nid = $_REQUEST['nid'];
                    $tnode = Node::load($nid);
                    $ret .= "<br / >with body:" . $_REQUEST['body'] . " replacing: <br />";
                    $ret .=$tnode->body->value;
                    $tnode->body->value =  $_REQUEST['body'];
                    //$tnode->save();
                }
                $ret .= "<div class='closeWindow'></div>";

                return [
                    '#markup' => t($ret),
                    '#attached' => [
                        'library' => [
                            'spj_customimport/import',
                        ],
                    ]
                ];

            } else {

                            
            //&ref=REF=999&nid=4654
            $ref = "https://spj.org/newsLoad.asp?REF=" . $_REQUEST['ref'];
            $nid = $_REQUEST['nid'];
            //$res = "<textarea>" . file_get_contents($ref) . "</textarea>";
            $urlToload = $ref;
            $res = file_get_contents($ref);

            $trim = substr(strpos($res, "newsBody"), strpos($res, "END"));

            $ret = "loading info for  " . $ref . "<textarea class='form-control'>" . $res . "</textarea>";
            }

            return [
                '#markup' => t($ret),
                '#attached' => [
                    'library' => [
                        'spj_customimport/import',
                    ],
                ]
            ];
                    
               
        } else {

        
            if($isScrape){
                $pageResult = "";
                    if($type=="inetdiff"){


                        $ret .= "Match reach row with a record in the DB<br /><table class='table table-bordered'><tr><th>Title</th><th>ref</th><th>nid</th></tr>";
                        
                        /*
                        $xml = simplexml_load_file("https://spj.org/newsImportXml.xml");
                        foreach($xml->channel->item as $item){
                            $title= $item->title;
                            $link= $item->link;

                            $nodes = \Drupal::entityTypeManager()
                            ->getStorage('node')->getQuery();

                                $nodes->condition('type', 'news_item');
                                //$nodes->condition('field_legacy_uri', );
                                $nodes->condition('title', '%' . $title . '%', "like");
                                //$nodes->condition('title', $title);
                                $nids = $nodes->execute();

                                if(count($nids)==1){
                                    
                                    foreach($nids as $nid){
                                        $tnode = Node::load($nid);
                                        $tnode->field_legacy_uri = $link;
                                        //$tnode->save();
                                    }
                                    
                                }
                            
                            $ret .= "<tr><td>" . $title . "</td><td>" . $link . "</td><td nowrap>found: " . count($nids) . "</td></tr>";

                        }
                        */

                        

                        
                        $nodes = \Drupal::entityTypeManager()
                                        ->getStorage('node')->getQuery();

                            $nodes->condition('type', 'news_item');
                            $nodes->sort('field_legacy_uri', 'DESC');
                            $nids = $nodes->execute();
                            
                            $count=1;
                            foreach($nids as $nid){
                                if($nid){
                                    $tnode = Node::load($nid);

                                    $body = $tnode->body->value;
                                    if(trim($tnode->field_legacy_uri->value)==""){

                                        $ret .="<tr>";
                                        $ret .="<td>" . $count . ") " .  $tnode->title->value . "</td>";
                                        
                                        $ret .="<td>" . $tnode->field_legacy_uri->value . "</td>";
                                        $ret .="<td><a id='node_" . $nid . "' href=\"javascript:customimport.fetchNews('". $tnode->field_legacy_uri->value ."','" . $nid . "')\" class='fetchNews' data-ref='" . $tnode->field_legacy_uri->value . "' data-nid=" . $nid ."'>" .  $nid  . "</a>";
                                        $ret .= "<form action='/customimport/fetchnews' target='_blank' method='post'><input type='hidden' name='post' value='true' /><input type='hidden' name='nid' value='" . $nid . "' /><textarea name='body'></textarea></form></td>";
                                        $ret .="</tr>";

                                        $count++;
                                    }
                                }
                            }
                            

                        $ret .="</table>";
                        /*
                        $ret .= "Load csv " . $urlToload;
                        $CSVfp = fopen($urlToload, "r");
                        $count = 0;
                        $counter = 1;

                        

                        if($CSVfp !== FALSE) {
                            while(! feof($CSVfp)) {
                                
                                $data = fgetcsv($CSVfp, 1000, ",");
                                
                                if($count>0){
                                    $title = $data[0];
                                    $database = \Drupal::database();
                                    
                                    
                                    $sql = `SELECT "node_field_data"."langcode" AS "node_field_data_langcode", "users_field_data_node_field_data"."langcode" AS "users_field_data_node_field_data_langcode", "node_field_data"."nid" AS "nid", "users_field_data_node_field_data"."uid" AS "users_field_data_node_field_data_uid"
                                        FROM
                                        {node_field_data} "node_field_data"
                                        INNER JOIN {users_field_data} "users_field_data_node_field_data" ON node_field_data.uid = users_field_data_node_field_data.uid
                                        WHERE ("node_field_data"."title" LIKE '%hi%' ESCAPE '\\') AND ("node_field_data"."type" IN ('news_item')) AND ((node_field_data.status = 1 OR (node_field_data.uid = 1 AND 1 <> 0 AND 1 = 1) OR 1 = 1))
                                        ORDER BY "node_field_data"."changed" DESC
                                        LIMIT 50 OFFSET 0`;

                                
                                    //$con = \Drupal\Core\Database\Database::getConnection('Default');
                                
                                    //$query = $con->query($sql);
                                    //$result = $query->fetchAll();
                                    $pos =  strpos($title, "'");
                                    $newtitle = substr($title, 0, $pos);
                                    $newtitle = str_replace("'", "’",$newtitle);

                                    
                                    $nodes = \Drupal::entityTypeManager()
                                        ->getStorage('node')->getQuery();

                                    $nodes->condition('type', 'news_item');
                                    $nodes->condition('title', '%' . $newtitle . '%', "like");
                                    $nids = $nodes->execute();


                                
                                    if(count($nids)!=-1){
                                        // deleted original, or its new and doesn't have any matched content
                                        $ret .= "<br />" . $counter . ")  <code>" . $newtitle . "</code> from <br /><code>" . $title . "</code><br />";
                                        $ref = $data[2];
                                        $id= $data[3];
                                        $ret .= "ref: " . $ref . " | node id: " . $id; 
        
                                        $ret .= "<br />Found: " . count($nids) . "<hr />";
                                        $counter++;
                                    } else {
                                        //$ret .= "<br />Only one of <code>" . $newtitle . "</code> from <br /><code>" . $title . "</code><hr />";
                                    }

                                    */

                                    /*
                                    if(count($nids)==2){
                                        $ret .= "searching for <code>" . $newtitle . "</code> from <br /><code>" . $title . "</code><br />";
                                        $ref = $data[2];
                                        $id= $data[3];
                                        $ret .= "ref: " . $ref . " | node id: " . $id; 

                                        $ret .= "<br />Found: " . count($nids) . "<br />";

                                        $ret .=  "<pre style='display: none'>";
                                        $da = print_r($data, true);
                                        $ret .= $da;
                                        $ret .= "</pre><br />";


                                        foreach($nids as $nid){
                                            if($nid!=$id){
                                                $tnode = Node::load($nid);
                                                $tnode->field_legacy_uri = $ref;
                                                //$tnode->save();
                                            } else {
                                                $tnode = Node::load($nid);
                                                $tnode->delete();
                                            }

                                        }
                                        $ret .="<hr />";
                                    }
                                    */
                                /* 
                                }
                                $count++;
                            }
                        }
                        fclose($CSVfp);
                        */

                    } else {

                        

                        $pageResult = CustomImportController::scrapePage($urlToload);
                    
                    

                    $pageRes =$this->prepareScrape($type);
                    
                    $ret .=  '<h3>Let us import some content!</h3><h2>' .$type .  '</h2>';
                    $ret .=  $backLink;
                    $ret .= $pageRes;
                    if(isset($_REQUEST['ajax'])){
                        $ret .= "<hr /><textarea id='loaded'>" . $pageResult . "</textarea>";
                    } else {
                        $ret .= "<form method='POST'><input type='hidden' name='ajax' value='true' /><input type='hidden' name='runImport' value='true' />
                            <textarea name='data' class='form-control' id='loadData' placeholder='hit the load button to populate'></textarea>
                            <input type='submit' value='Run Import' class='btn btn-secondary' name='importScrapeButton' /></form>
                            ";
                    }

                    if(isset($_REQUEST['importScrapeButton'])){
                        // actually import
                        $ret .="<hr />Actually Import"; 
                        $ret .= "<ul>";

                        $datArr = json_decode($_REQUEST['data']);
                        foreach( $datArr as $dat){

                            $title = $dat->title;
                            /*
                            $field_title = $dat->field_title;
                            $field_email = $dat->field_email;
                            $field_twitter = $dat->field_twitter;
                            $body = $dat->body;
                            $image = $dat->field_profile_image;
                            */

                            $ret .= "<li>";
                            $ret .= $title ." | ";
                            /*
                            $ret .= $field_title ." | ";
                            $ret .= $field_email ." | ";
                            //$ret .= $body . " | " .
                            $ret .= $image ." | ";
                            $ret .= $field_twitter ."<br />";
                            */

                            $ret .= "<strong> ". $this->checkIfNodeExists($dat, $type) . "</strong>";
                            /*
                            $ret .= "<pre style='display: none'>";
                            $ret .= print_r($dat, true);
                            $ret .= "</pre>";
                            */

                            $ret .="</li>";
                            }
                        $ret .= "</ul>";
                        }
                    }

                
            } else {
                if($urlToload!==""){
                    $ret .= "Import content into this glorious system!<br />";
                    $ret .=  $backLink;
                    if(isset($_REQUEST['importSubmit'])){
                        dpm($_REQUEST);
                        $ret .= $this->loadRSS($urlToload, $type);

                    } else {
                        $ret .= "<br />Click Run Import to, well, run the import<br />";
                    }
                } else {
                    
                    $ret .= "Please choose better";
                    if($showMenu){
                        $ret .= "<ul>";
                            $ret .= "<li><a href='/customimport/news'>Latest News</a></li>";
                            $ret .= "<li><a href='/customimport/newsawards'>Old News</a></li>";
                            $ret .= "<li><a href='/customimport/newsinet'>Old News Inet</a></li>";
                            $ret .= "<li><a href='/customimport/inetdiff'>Inet dups</a></li>";
                            $ret .= "<li><a href='/customimport/leads'>LEADS</a></li>";
                            $ret .= "<li><a href='/customimport/hq'>Hq Bios</a></li>";
                            $ret .= "<li><a href='/customimport/whistle'>Whistleblower Bios</a></li>";
                            $ret .= "<li><a href='/customimport/foundation'>SPJ Foundation Bios</a></li>";
                            $ret .= "<li><a href='/customimport/stc'>Student Trustee Council Bios</a></li>";
                            $ret .= "<li><a href='/customimport/board'>Board of Directors Bios</a></li>";
                            $ret .= "<li><a href='/customimport/rc'>Regional Coordinators Bios</a></li>";
                            $ret .= "<li><a href='/customimport/calendar'>Calendar Events</a></li>";
                            $ret .= "<li><a href='/customimport/foi'>FOI</a></li>";
                        $ret .="</ul>";
                    }
                }
            }

            if(!isset($_REQUEST['ajax2'])){
                if(isset($_REQUEST['importSubmit'])){
                
                } else {

                    if($showSubmit==true){ 
                    $ret .="<br /><form>";
                    $ret .="<input type=\"submit\" class=\"btn btn-primary\" name=\"importSubmit\" value=\"Run Import\" />
                        </form>";
                    }
                    
                }

                return [
                    '#markup' => t($ret),
                    '#attached' => [
                        'library' => [
                            'spj_customimport/import',
                        ],
                    ]
                ];
            } else {
                
                return [
                    '#markup' => $ret,
                ];
            }
        
        }
    }

}