<?php 
namespace Drupal\spj_external_feeds\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;


/**
 * Provides a 'SPJ Quill Feeds' block.
 *
 * @Block(
 *  id = "spj_quill_feed_block",
 *  label = "SPJ Quill Feeds",
 *  admin_label = @Translation("SPJ Quill Feed"),
 * )
 */
class SpjFeedQuillBlock extends BlockBase  {

    use UncacheableDependencyTrait;


    function build(){

        \Drupal::service('page_cache_kill_switch')->trigger();
        $path = getUrl();
        $feedUrl = getFeedUrl("Quill");
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