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
        return getYoutubeRssUrlFromPath($path);
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

        $data["message"] = getMessage($path);
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