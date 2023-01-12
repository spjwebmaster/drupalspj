<?php 
namespace Drupal\spj_redirect\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\Core\Url;

class SpjRedirectController extends ControllerBase {


        public function index(){

            $path = \Drupal::service('path.current')->getPath();
            $queryParams = \Drupal::request()->query->all();

            $ret = "<h1>Page not found.... yet</h1>";
            $pathparam = "?" . http_build_query($queryParams);
            $ret .= "Searching '" . $path . " | " . $pathparam . "'";
      
            /*
            function mymodule_entity_insert(EntityInterface $entity) {
            if ($entity->getType() == 'article') {
                (new RedirectResponse('/node/' . ($entity->id())))->send();
                exit();
            }

            */
            $patterns = [
                "/index"=>"&lt;front&gt;",
                "/ethicscode"=>"/ethics/codeofethics"
            ];
            $match = "none";

            foreach($patterns as $pattern=>$uri){

                if($pattern== $path){
                    $match = $uri;
                    $ret .= "<br />match: ". $match;
                }
            }
            $url = "";
            if($match!=="none"){

                $url_object = \Drupal::service('path.validator')->getUrlIfValid($match);
	            $route_name = $url_object->getRouteName();

                $url = Url::fromRoute($route_name);
                $ret .= " | url ". $route_name . " : " . $url->toString(); 
            } else {

            }
            
                //$url = Url::fromRoute('entity.node.canonical', ['node' => 1]);
                //return new RedirectResponse($url->toString());


            

            

       
            return [
                '#markup' => t($ret),
                '#attached' => [
                    'library' => [
                        'spj_redirect/spjredir',
                    ],
                ]
            ];
            
        }
}