<?php 
namespace Drupal\spjcalendar\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Events Double' block.
 *
 * @Block(
 *  id = "spj_calendar_block3",
 *  label = "SPJ Calendar 3",
 *  admin_label = @Translation("SPJ Calendar 3"),
 * )
 */
class CalendarBlock3 extends BlockBase  {

   
    public function readRSS($url){
        $feed = simplexml_load_file($url);

        $markup = "";
        $max = 3;
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

        $calrss = "https://feeds.feedburner.com/spjcalendarspj";
        $generalrss = "https://feeds.feedburner.com/calendargeneraljournalism";
        
        $calRes = CalendarBlock3::readRSS($calrss);
        $generalRes = CalendarBlock3::readRSS($generalrss);

        $markup = "<p>Is your organization hosting an event you think would be of interest to journalists? Add it to our calendar!</p><div class='row'><div class='col-sm-2'><picture><img src='/sites/default/files/2022-04/t-spjheart-teal.jpg' alt='SPJ Heart' title='SPJ heart' style='width: 100%;' /></picture></div><div class='col-sm-6'>";
        $markup .= "<div class='item-list'><strong>Upcoming SPJ events</strong><br /><ul class='item-list'>";
        foreach($calRes as $cal){
            $markup .= "<li>" . $cal['title'] . "</li>";
        }

        $markup .= "</ul><p><br /></p><strong>General events</strong><br /><ul class='item-list'>";
        foreach($generalRes as $cal){
            $markup .= "<li>" . $cal['title'] . "</li>";
        }
        $markup .= "</ul>";
        $markup .= "</div></div></div>";
       
       
        return [
            '#type' => 'markup',
            '#markup' => $markup,
            '#attached' => [
                'library' => [
                  'spjswiper/swiper',
                ],
            ]
        ];
    }
}