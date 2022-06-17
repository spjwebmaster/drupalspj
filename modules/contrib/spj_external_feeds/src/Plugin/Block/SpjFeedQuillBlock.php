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

   

    function getFeedUrl($path) {
        $feedUrl = "";
        switch($path){
            case "diversity": 
                $feedUrl = "https://feeds.feedburner.com/quill/diversity"; 
                break;
            case "ethics":
                $feedUrl = "https://feeds.feedburner.com/quill/ethics";
                break;
            case "foi": 
                $feedUrl = "https://feeds.feedburner.com/quill/foi";
                break;
            case "freelance": 
                $feedUrl = "https://feeds.feedburner.com/quill/freelancing";
                break;
            case "resourcestoolsfreelancers": 
                $feedUrl = "https://feeds.feedburner.com/quill/freelancing";
                break;
            case "international": 
                $feedUrl = "https://feeds.feedburner.com/quill/ij";
                break;
            case "resourcespublications": 
                $feedUrl = "https://feeds.feedburner.com/spj-quill";
                break;
            case "resourcestoolsteachers": 
                $feedUrl = "https://feeds.feedburner.com/quill/students";
                break;
            case "resourcestoolsstudents": 
                $feedUrl = "https://feeds.feedburner.com/quill/students";
                break;
            default:
                $feedUrl = "https://www.quillmag.com/feed/";
                break;
        }
        return $feedUrl;
    }



    function build(){

        \Drupal::service('page_cache_kill_switch')->trigger();
        $path = getUrl();
        $feedUrl = SpjFeedQuillBlock::getFeedUrl($path);
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