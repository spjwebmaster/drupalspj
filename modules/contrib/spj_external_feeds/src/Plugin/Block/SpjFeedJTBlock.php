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

   

    function getFeedUrl($path) {
        $feedUrl = "";
        switch($path){
            case "diversity": 
                $feedUrl = "http://feeds.feedburner.com/jtb-diversity"; 
                break;
            case "ethics":
                $feedUrl = "http://feeds.feedburner.com/jtb-ethics";
                break;
            case "foi": 
                $feedUrl = "http://feeds.feedburner.com/jtb-foi";
                break;
            case "freelance": 
                $feedUrl = "http://feeds.feedburner.com/jtb-freelance";
                break;
            case "resourcestoolsfreelancers": 
                $feedUrl = "http://feeds.feedburner.com/jtb-freelance";
                break;
            case "international": 
                $feedUrl = "http://feeds.feedburner.com/jtb-ij";
                break;
            case "resourcestoolsteachers": 
                $feedUrl = "http://feeds.feedburner.com/jtb-jed";
                break;
            case "resourcestoolsstudents": 
                $feedUrl = "http://feeds.feedburner.com/jtb-jed";
                break;
            default:
                $feedUrl = "https://www.journaliststoolbox.org/feed/";
                break;
        }
        return $feedUrl;
    }



    function build(){

        \Drupal::service('page_cache_kill_switch')->trigger();
        $path = getUrl();
        $feedUrl = SpjFeedJTBlock::getFeedUrl($path);
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