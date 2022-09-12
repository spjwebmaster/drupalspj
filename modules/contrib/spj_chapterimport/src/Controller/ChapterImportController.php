<?php 
namespace Drupal\spj_chapterimport\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use \Drupal\Core\File\FileSystemInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;


class ChapterImportController extends ControllerBase {



  

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



    private function createNode($data, $type){

        $machineType = $type;

        $retId = "";
        $dataArray = null;
      

            $title = trim($data->title);

            $dataArray = array(
                'type' => $machineType,
                'title' => $title,
                'langcode' => 'en',
                 //'uid' => $node->post_id,
                'status' => 1,
                'field_role' => array(
                    'target_id' => 1
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
            $node->save();
            $nid = $node->id();
            $retId = $nid;

      
    }

   


    public function import($type){

        $ret = "";
         
    }

}