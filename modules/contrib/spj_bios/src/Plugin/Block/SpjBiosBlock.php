<?php 
namespace Drupal\spj_bios\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;


/**
 * Provides a 'SPJ Bios' block.
 *
 * @Block(
 *  id = "spj_bios_block",
 *  label = "SPJ Bios",
 *  admin_label = @Translation("SPJ Bios"),
 * )
 */
class SpjBiosBlock extends BlockBase  {


    
    private function getBios($nids, $termIds, $heirarchyTerms, $tid){
        $ret = [];

        //dpm($heirarchyTerms);
        //\Drupal::entityManager()->getStorage('node')->resetCache($nids);
        $nodes = Node::loadMultiple($nids);
        foreach($nodes as $node){
            //dpm($node);


            $profileId = $node->get("field_profile")->target_id;
            //$style = ImageStyle::load('sample_image_style');
            if($profileId){
            $media = Media::load($profileId);
            $fid = $media->field_media_image->target_id;
            $file = File::load($fid);

            $url = $file->getFileUri();
            $imgurl = ImageStyle::load('medium')->buildUrl($url);
            } else {
                $imgurl = "";
            }

            $roles =null;
            // spj foundation board
            if($tid == 172){
                $list = [];
                foreach($node->get("field_role") as $role){
                    
                    $rid = $role->target_id;
                    if($rid!=$tid){
                        $list[] = $rid;
                    } 
                }

                // spj foundation officers
                if(in_array(181, $list)){
                    foreach($list as $listItem){
                        if (!in_array($listItem, $heirarchyTerms)) {
                            
                            $roles .= $termIds[$listItem];
                        }
                        
                    }
                    //$roles .= "list: " . implode(",",$heirarchyTerms);
                }
            }

            $thisNode = array(
                "title"=>$node->get("title")->value,
                "field_title"=>$node->get("field_title")->value,
                "field_email"=>$node->get("field_email")->value,
                "field_twitter"=>$node->get("field_twitter")->value,
                "body"=>$node->get("body")->value,
                "field_profile" => $imgurl,
                "field_role"=>$roles
            );

            //dpm($node->get("field_role"));
            $ret[] = $thisNode;
        }

        //dpm($ret);
        return $ret;
    }
   
    public function build() {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $thisURL = $_SERVER['REQUEST_URI'];
	    $splits = explode("/",$thisURL);
        $tag = $splits[count($splits)-2];
        $tid = 0;
        $isRoot = false;
        switch($tag){
            case "spj": $tid = 6; $isRoot = true; break;
            case "whistleblower": $tid = 248; break;
            case "foundation": $tid = 172; break;
        }
        dpm($tag);
        dpm($tid);

        $query = \Drupal::entityQuery('taxonomy_term');
        $query->condition('vid', "bio_role_association");
        $query->sort("weight");
        //$query->condition('parent', $tid);
        $tids = $query->execute();
        $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
        $master = [];
        $data = [];
        $view = [];
        $termIds = [];
        $heirarchyTerms = [];
        //$heirarchyTerms[] = $tid;

        foreach($terms as $term){
            //$markup .= $term->name[0]->value . "| " . $term->tid[0]->value . "<br />";
            //$markup .= $term->parent[0]->target_id . "<hr />";
            $parentTid = $term->parent[0]->target_id;
            $testtid = $term->tid[0]->value;
            $termIds[$testtid] = $term->name[0]->value;

            
                // to include root taxonomy term, add || $testtid == $tid
            if($parentTid == $tid ){

                // what about children of the main?
                $master[] = array("name"=>$term->name[0]->value, "term" => $term);
                $view[] = array("name"=>$term->name[0]->value, "term" => $testtid);
                $heirarchyTerms[] = $term->tid[0]->value;

            }
            
            
        }

        
        $dups = [];
        foreach($master as $entry) {
            $thistid = $entry["term"]->tid[0]->value;
            $thisname = $entry["name"];

            if(!in_array($thistid, $dups)){
                $dups[] = $thistid;
            
          
            
                // new query of bios with this tag
                $query = \Drupal::entityQuery('node');
                $query->condition('type', "bio");
                $query->condition('field_role.entity.tid',$thistid);
                $tids = $query->execute();

                // dedup?
                $bioData = SpjBiosBlock::getBios($tids, $termIds, $heirarchyTerms, $tid);
                $dataArr = array(
                    "tid" => $thistid,
                    "entityids" => implode( ",",$tids),
                    "entities" => $bioData
                );
                $data[$thisname] = $dataArr;
                //dpm($dataArr);
            }
        }
        

        //dpm($data);
       
        // send as data instead
        return [
            '#theme' => 'spj_bios_block',
            '#data' => $data
            
        ];
    }
}