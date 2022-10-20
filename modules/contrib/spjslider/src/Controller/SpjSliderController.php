<?php 
namespace Drupal\spjslider\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;



class SpjSliderController extends ControllerBase {

    public function index(){

        $path = "home";
        if(isset($_REQUEST['path'])){
            $path = $_REQUEST['path'];
        }

        $dataAll = buildBanner($path);
        $data = $dataAll["data"];
        return new JsonResponse([ 'data' => $data, 'method' => 'GET', 'status'=> 200]);
    }

}