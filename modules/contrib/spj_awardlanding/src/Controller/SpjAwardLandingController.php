<?php 
namespace Drupal\spj_awardlanding\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;


class SpjAwardLandingController extends ControllerBase {

    public function landing($name){
        return [
            '#type' => 'markup',
            '#markup' => $this->t('<h3>@name</h3>', [
		        '@name' => $name
		    ]),
        ];
    }

}