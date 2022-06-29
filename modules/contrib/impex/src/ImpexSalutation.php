<?php
namespace Drupal\impex;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Form\FormState;


class ImpexSalutation {

    use StringTranslationTrait;

    public function getSalutation(){

        $formID = 'salutation_configuration_form';
        $form_state = new FormState();
        $form_state->setRebuild();


        $config = \Drupal::config('impex.custom_salutation');
        
        $sal = $config->get("salutation");

        $ret = $sal;
      

        $time = new \DateTime();
        if((int)$time->format('G') > 0 && (int)$time->format('G') < 12){
            return $this->t((int)$time->format('G') .  '|' . (int)$time->format('g') . ': Magandang Umaga! ' . $ret);
        }
        if((int)$time->format('G') >= 12 && (int)$time->format('G') < 18){
            return $this->t((int)$time->format('G') .  '|' . (int)$time->format('g') . ': Magandang Hapon! ' .$ret);
        }
        if((int)$time->format('G') >= 18 ){
            return $this->t((int)$time->format('G') . '|' . (int)$time->format('g') . ' : Magandang Gabi! ' . $ret);
        }
    }
}