<?php 
namespace Drupal\spj_impexium\Plugin\Block;
use Drupal\Core\Block\BlockBase;



/**
 * Provides a 'SPJ Impexium Lookup' block.
 *
 * @Block(
 *  id = "spj_impex_block",
 *  label = "SPJ Impexium Block",
 *  admin_label = @Translation("SPJ Impexium Block"),
 * )
 */
class SpjImpexBlock extends BlockBase  {

    public function build(){
     
        return [
            '#markup' => '<div class="spjimpexLookup"></div>',
            '#attached' => [
                'library' => [
                  'spj_impexium/api',
                ]
            ],
        ];
    }

}
