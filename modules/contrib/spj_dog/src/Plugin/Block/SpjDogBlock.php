<?php 
namespace Drupal\spj_dog\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Day of Giving' block.
 *
 * @Block(
 *  id = "spj_dog_block",
 *  label = "Showing Spj Day of Giving Leaderboard",
 *  admin_label = @Translation("SPJ DOG Block"),
 * )
 */
class SpjDogBlock extends BlockBase  {
    function build(){

        return [
            '#markup' => '<div class="spjdog"></div>',
            '#attached' => [
                'library' => [
                  'spj_dog/leaderboard',
                ]
            ],
        ];
    }
}