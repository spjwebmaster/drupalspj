<?php 
namespace Drupal\spj_external_feeds\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;



class SpjexternalfeedsController extends ControllerBase {


    public function index($type){

        $path = "";
        if(isset($_REQUEST['path'])){
            $path = $_REQUEST['path'];
        }
        $data = [];
        $data["type"] = $type;
        $url = getFeedUrl($type);
        $data['url'] = $url;
        $data['path'] = $path;
        $feedData = getFeedData($url);
        $data['feed'] = $feedData;
    
        return new JsonResponse([ 'data' => $data, 'method' => 'GET', 'status'=> 200]);
    }


}