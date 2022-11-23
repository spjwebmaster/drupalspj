<?php 
namespace Drupal\spj_webform_payment\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class SpjWebformPaymentController extends ControllerBase {


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

   


    public function index(Request $request){

        $message = "Award PMT Controller Hit" . print_r($request, true);
        
        \Drupal::logger('spj_webform_payment')->warning($message);
        return [
            '#markup' => t($message)
        ];
         
    }

}