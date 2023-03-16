<?php 
namespace Drupal\spjslider\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Provides a 'SPJ Slider' block.
 *
 * @Block(
 *  id = "spj_slider_block",
 *  label = "SPJ Slider (Swiper JS)",
 *  admin_label = @Translation("SPJ Slider"),
 * )
 */
class SliderBlock extends BlockBase  {

  /**
   * {@inheritdoc}
   */
    public function build() {

        $current_url = Url::fromRoute('<current>');
        $path = $current_url->toString();
        $dataAll = buildBanner($path);
        $data = $dataAll["data"];
        $isSingle = $dataAll['isSingle'];
        
        
        //$output .= print_r($ids, true);
        return [
            //'#type' => 'markup',
            '#theme' => 'slider_block',
            '#single' => $isSingle,
            '#data' => $data,
            '#attached' => [
                'library' => [
                    'spjswiper/swiper',
                    'spjswiper/json'
                ],
            ]
        ];
    }
}