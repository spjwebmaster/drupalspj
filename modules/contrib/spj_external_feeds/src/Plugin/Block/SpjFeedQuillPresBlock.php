<?php 
namespace Drupal\spj_external_feeds\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;


/**
 * Provides a 'SPJ Quill President Feeds' block.
 *
 * @Block(
 *  id = "spj_quill_president_feed_block",
 *  label = "SPJ Quill President Feeds",
 *  admin_label = @Translation("SPJ Quill President Feed"),
 * )
 */
class SpjFeedQuillPresBlock extends BlockBase  {

    use UncacheableDependencyTrait;

   

    function getFeedUrl($path) {
        $feedUrl = "";
        $feedUrl = "https://feeds.feedburner.com/presidentquill"; 
        return $feedUrl;
    }

    

    function build(){

        \Drupal::service('page_cache_kill_switch')->trigger();
        $path = getUrl();
        $feedUrl = SpjFeedQuillPresBlock::getFeedUrl($path);
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