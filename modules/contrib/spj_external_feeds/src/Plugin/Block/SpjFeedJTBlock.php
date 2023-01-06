<?php 
namespace Drupal\spj_external_feeds\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;


/**
 * Provides a 'SPJ Journalist's Toolbox Feeds' block.
 *
 * @Block(
 *  id = "spj_jt_feed_block",
 *  label = "SPJ Journalist's Toolbox Feeds",
 *  admin_label = @Translation("SPJ JT Feed"),
 * )
 */
class SpjFeedJTBlock extends BlockBase  {

    use UncacheableDependencyTrait;


    function build(){

        \Drupal::service('page_cache_kill_switch')->trigger();
        $path = getUrl();
        $feedUrl = getFeedUrl("JT");
        if($feedUrl!=null){
            $data = getFeedData($feedUrl);

            $markup = spj_ext_feed_makeMarkup($data);
        } else {
            $markup = "";
        }
        return [
            '#markup' =>  $markup,

        ];
    }
}