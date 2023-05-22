<?php 
namespace Drupal\spj_webinars\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Webinars List' block.
 *
 * @Block(
 *  id = "spj_webinars_block",
 *  label = "SPJ Webinars List React",
 *  admin_label = @Translation("SPJ Webinars List React"),
 * )
 */
class SpjWebinarsBlock extends BlockBase  {

    function build(){

        return [
            '#markup' => '<div id="webinarList"></div>',
            '#attached' => [
                'library' => [
                  'spj_webinars/webinar',
                ]
            ],
        ];
    }
}