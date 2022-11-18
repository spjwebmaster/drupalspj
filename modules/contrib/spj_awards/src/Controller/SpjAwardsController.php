<?php 
namespace Drupal\spj_awards\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;


class SpjAwardsController extends ControllerBase {
    public function index($type){

        return "hi";
    }
    public function landing($name){

        return [
            '#type' => 'markup',
            '#markup' => $this->t('<h1>This category @category and the award @name</h1>', [
		        '@name' => $name
		      ]),
        ];
    }

}