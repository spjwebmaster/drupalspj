<?php 
namespace Drupal\spjads\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Ads' block.
 *
 * @Block(
 *  id = "spj_ads_block",
 *  label = "SPJ Ads",
 *  admin_label = @Translation("SPJ Ads"),
 * )
 */
class SpjadsBlock extends BlockBase  {

   
    public function build() {

       $markup = 'Ads here';
        return [
            '#type' => 'markup',
            '#markup' => $markup
        ];
    }
}