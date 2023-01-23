<?php 
namespace Drupal\spj_award_winners\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;


/**
 * Provides a 'SPJ Award Winners' block.
 *
 * @Block(
 *  id = "spj_award_winners_block",
 *  label = "SPJ Award Winners",
 *  admin_label = @Translation("SPJ Award Winners"),
 * )
 */
class SpjAwardWinnersBlock extends BlockBase  {




    function build(){

        return [
            '#markup' => '<div id="awardWinners"></div>',
            '#attached' => [
                'library' => [
                  'spj_award_winners/awardwinners',
                ]
            ],
        ];
    }
}