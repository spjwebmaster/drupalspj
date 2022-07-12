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
        
        /*
        #Press4Education 
        https://www.youtube.com/feeds/videos.xml?playlist_id=PLNitcNsxwFxJ5k-E1I9DBgVW9Ex3YOLbv

        Media Trust
        https://www.youtube.com/feeds/videos.xml?playlist_id=PLNitcNsxwFxLIOZmiivSYbMYw_2NLYPiL

        Webinars
        https://www.youtube.com/feeds/videos.xml?playlist_id=PLNitcNsxwFxLCwOI0LtJieM9af4HffJug

        Ethics 
        https://www.youtube.com/feeds/videos.xml?playlist_id=PLNitcNsxwFxIZFrr8e_NnnuPyCAnAOImA

        Freelance
        https://www.youtube.com/feeds/videos.xml?playlist_id=PLNitcNsxwFxLOROn2shliHB4MbIn2RQKq

        International
        https://www.youtube.com/feeds/videos.xml?channel_id=UCxudDPp37SBDBTNI9IJJB5w
        */

        
        switch($path){
            case "international": 
                $feedUrl = "https://www.youtube.com/feeds/videos.xml?channel_id=UCxudDPp37SBDBTNI9IJJB5w"; 
                break;

            case "freelance": 
                //diversity
                $feedUrl = "https://www.youtube.com/feeds/videos.xml?playlist_id=PLNitcNsxwFxLOROn2shliHB4MbIn2RQKq"; 
                break;
            default: 
                $feedUrl = "https://www.youtube.com/feeds/videos.xml?channel_id=UCxudDPp37SBDBTNI9IJJB5w";
                break;
                
        }
        return $feedUrl;
    }
    private function getMessage($path){

        // instead of hardcoding - get a $config object and form set up like the impex module
        $message = "";
        switch($path){

            case "international":
                $message = "Since 2020, the IC has held the #ICTalks to connect journalists and experts to our community at a time when it was difficult to do so in person. We have continued to host talks monthly that also include our #ICShorts. Check it out here if you missed tuning in live.";
                break;
            case "freelance":
                $message = "Freelance Webinars";
                break;
            default:  
                $message = "Message here";
           
        }
        return $message;
    }
    public function build() {
        $path = getUrl();
        $feedUrl = $this->getFeedUrl($path);
        
        $feed = simplexml_load_file($feedUrl);

        $data = [];
        $data["author"] = [];
        $data["author"]["name"] = $feed->title . " on Youtube";
        $data["author"]["uri"] = $feed->author->uri;
        $data['thumb'] = null;
        $data['entries'] = [];


        $my_view_name = 'related_content';
        $my_display_name = 'block_1';

        $blockRenderer = \Drupal::service('block_renderer');
        // Pass the ID of the block plugin you wish to render.
        $content_block = $blockRenderer->renderContentBlock('spjcalendarawardsdates');
        if($content_block){
            $data['block'] = $content_block;
            dpm($content_block);
        }


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