<?php 
namespace Drupal\spj_reactform\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;


/**
 * Provides a 'SPJ React Form' block.
 *
 * @Block(
 *  id = "spj_react_form_block",
 *  label = "SPJ React Form",
 *  admin_label = @Translation("SPJ React Form"),
 * )
 */
class SpjReactformBlock extends BlockBase  {




    function build(){

        return [
            '#markup' => '<div id="reactform2"></div>',
            '#attached' => [
                'library' => [
                  'spj_reactform/reactform',
                ]
            ],
        ];
    }
}