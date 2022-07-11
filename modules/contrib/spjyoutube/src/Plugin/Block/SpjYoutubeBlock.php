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
                default: 
                $feedUrl = "https://www.youtube.com/feeds/videos.xml?channel_id=UCxudDPp37SBDBTNI9IJJB5w";
                
        }
        return $feedUrl;
    }
    private function getMessage($path){

        // instead of hardcoding - get a $config object and form set up like the impex module
        $message = "";
        switch($path){
            default:  

            $message = "Since 2020, the IC has held the #ICTalks to connect journalists and experts to our community at a time when it was difficult to do so in person. We have continued to host talks monthly that also include our #ICShorts. Check it out here if you missed tuning in live.";
            break;
        }
        return $message;
    }
    public function build() {
        $path = getUrl();
        $feedUrl = $this->getFeedUrl($path);
        
        $feed = simplexml_load_file($feedUrl);

        $data = [];
        $data["author"] = [];
        $data["author"]["name"] = $feed->author->name;
        $data["author"]["uri"] = $feed->author->uri;
        $data['thumb'] = null;
        $data['entries'] = [];

        $data["message"] = $this->getMessage($path);
        // or render a view for multilingual support?

        $feed->registerXPathNamespace('yt', 'http://www.youtube.com/xml/schemas/2015');

        $counter = 1;
        $max = 5;
        foreach($feed->entry as $entry){
            if($counter<$max){
                $id = str_replace("yt:video:", "",$entry->id);
                $ob = [];
                if($counter==1){
                    $data['thumb']  = "https://i4.ytimg.com/vi/" . $id . "/hqdefault.jpg";
                } 
                $ob["title"] = $entry->title;
                $ob["id"] = $id;
                $ob["url"] = "https://www.youtube.com/watch?v=" . $id;
                
                $data['entries'][] = $ob;
            }
            $counter++;
           
        }        


        return [
            '#theme' => 'spjyoutube_block',
            '#data' => $data
            
        ];

    }
}