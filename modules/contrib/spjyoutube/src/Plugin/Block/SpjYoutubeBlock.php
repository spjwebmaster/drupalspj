<?php 
namespace Drupal\spjyoutube\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Youtube Feed' block.
 *
 * @Block(
 *  id = "spj_youtube_feed_block",
 *  label = "SPJ Youtube Feed",
 *  admin_label = @Translation("SPJ Youtube Feed"),
 * )
 */
class SpjYoutubeBlock extends BlockBase  {

    private function getFeedUrl($path) {
        $feedUrl ="";
        switch($path){
            case "9": 
                //diversity
                $feedUrl = "https://feeds.feedburner.com/quill/diversity"; 
                break;
           
        }
        return $feedUrl;
    }
    public function build() {
        $path = getUrl();
        $feedUrl = $this->getFeedUrl($path);
        
        $feed = simplexml_load_file($feedUrl);

        $data = [];
        $data["author"] = [];
        $data["author"]["name"] = $feed->author->name;
        $data["author"]["uri"] = $feed->author->uri;
        $data['entries'] = [];
        foreach($feed->entry as $entry){
            $ob = [];
            $ob["title"] = $entry->title;
            $ob["id"] = $entry->id;
            $ob["url"] = "";
            $data['entries'][] = $ob;
            
        }        


        return [
            '#theme' => 'spjyoutube_block',
            '#data' => $data
            
        ];

    }
}