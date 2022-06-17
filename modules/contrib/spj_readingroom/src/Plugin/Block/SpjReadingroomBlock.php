<?php 
namespace Drupal\spj_readingroom\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;


/**
 * Provides a 'SPJ Reading Room Feeds' block.
 *
 * @Block(
 *  id = "spj_rr_feed_block",
 *  label = "SPJ Reading Room Feeds",
 *  admin_label = @Translation("SPJ RR Feed"),
 * )
 */
class SpjReadingroomBlock extends BlockBase  {

    use UncacheableDependencyTrait;

   

    function getFeedUrl($path) {
        $feedUrl = "";
        switch($path){
            case "9": 
                //diversity
                $feedUrl = "https://feeds.feedburner.com/quill/diversity"; 
                break;
            case "7":
                //ethics
                $feedUrl = "https://feeds.feedburner.com/quill/ethics";
                break;
            case "8": 
                //foia
                $feedUrl = "https://feeds.feedburner.com/quill/foi";
                break;

            case "11": 
                //students
                $feedUrl = "https://feeds.feedburner.com/quill/students";
                break;

            case "16": 
                //educators
                $feedUrl = "https://feeds.feedburner.com/quill/students";
                break;
    
            case "12": 
                //yj
                $feedUrl = "https://feeds.feedburner.com/quill/genj";
                break;

            case "10": 
                //freelance
                $feedUrl = "https://feeds.feedburner.com/quill/freelancing";
                break;

            case "13": 
                //international;
                $feedUrl = "https://feeds.feedburner.com/quill/ij";
                break;
    
            default:
                $feedUrl = "https://feeds.feedburner.com/spj-quill";
                break;
        }

        return $feedUrl;
    }



    function build(){

        \Drupal::service('page_cache_kill_switch')->trigger();
        $uid = \Drupal::request()->query->get('field_news_tags_target_id');
        $data = null;
        if($uid){
            $feedUrl = SpjReadingroomBlock::getFeedUrl($uid);
            if($feedUrl!=null){
                $data = getRrFeedData($feedUrl);
            } 
        }

        //dpm($data);
        return [
            '#theme' => 'spj_readingroom_block',
            '#data' => $data
        ];
    }
}