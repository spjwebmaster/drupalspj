<?php 
namespace Drupal\Spjyoutube\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;



class SpjyoutubeController extends ControllerBase {

    public function index(){

        $path = "";
        if(isset($_REQUEST['path'])){
            $path = $_REQUEST['path'];
        }
        $url = getYoutubeRssUrlFromPath($path);
        $feedData = getYoutubeRss($url, $path);
        $data = [];
        $data['path'] = $path;
        $data['url'] = $url;
        $data['feed'] = $feedData;
        
    
        return new JsonResponse([ 'data' => $data, 'method' => 'GET', 'status'=> 200]);
    }

}