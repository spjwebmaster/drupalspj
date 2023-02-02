<?php 
namespace Drupal\spj_ads\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Ads' block.
 *
 * @Block(
 *  id = "spj_ads_block",
 *  label = "Showing Spj Ads",
 *  admin_label = @Translation("SPJ Ad Block"),
 * )
 */
class SpjAdsBlock extends BlockBase  {
    function build(){

        return [
            '#markup' => '<div class="spjad"></div>',
            '#attached' => [
                'library' => [
                  'spj_ads/ads',
                ]
            ],
        ];
    }
}