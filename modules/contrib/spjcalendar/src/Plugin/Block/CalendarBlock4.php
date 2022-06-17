<?php 
namespace Drupal\spjcalendar\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Awards Important Date' block.
 *
 * @Block(
 *  id = "spj_calendar_block_awards",
 *  label = "SPJ Calendar Awards Dates",
 *  admin_label = @Translation("SPJ Calendar Awards Dates"),
 * )
 */
class CalendarBlock4 extends BlockBase  {

   
    public function readRSS($url){
        $feed = simplexml_load_file($url);

        $markup = "";
        $max = 15;
        $count = 0;
        $obj = array();
        foreach ($feed->channel->item as $item) {
            if($count<$max){
                $obj[$count]['title'] = (string) $item->title;
                $obj[$count]['description'] = (string) $item->description;
                $obj[$count]['eventstart'] = (string) $item->eventstart;
                $obj[$count]['eventend'] = (string) $item->eventend;
                $obj[$count]['link'] = (string) $item->link;
            }
            $count++;
        }
        return $obj;

    }
    public function build() {

        $calrss = "http://feeds.feedburner.com/spjcalendarawards";
    
        $calRes = readRSS($calrss, 15);
        $markup = "<div class='bluebox'><div class='item-list'><ul>";
        
        foreach($calRes as $cal){
            $markup .= "<li class='item-list'><a href='" . $cal['link'] . "'>" . $cal['eventstart'] . "<br /> ". $cal['title'] . "</a></li>";
        }

        $markup .= "</ul></div></div>";
       
       
        return [
            '#type' => 'markup',
            '#markup' => $markup,
            
        ];
    }
}