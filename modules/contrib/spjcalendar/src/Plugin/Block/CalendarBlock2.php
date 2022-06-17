<?php 
namespace Drupal\spjcalendar\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Calendar' block.
 *
 * @Block(
 *  id = "spj_calendar_block_calendar",
 *  label = "SPJ Calendar Block",
 *  admin_label = @Translation("SPJ Calendar Block"),
 * )
 */
class CalendarBlock2 extends BlockBase  {

   
    public function build() {

        
        $calrss = "http://calendar.spjnetwork.org/feed.php?ex=";
        $feed = simplexml_load_file($calrss);

        $markup = "";
        $max = 50;
        $count = 0;
        $markup .= "<div class='inputs'>";
        foreach ($feed->channel->item as $item) {
            if($count<$max){
                $title = (string) $item->title;
                $description = (string) $item->description;
                $eventstart = (string) $item->eventstart;
                $eventend = (string) $item->eventend;
                $link = (string) $item->link;
                $GUID = $item->GUID;
                $author = $item->author;


                $markup .= "<input type='hidden' name='" . $author . "'";
                $markup .= "value='{link:\"" . $link . "\", start:\"" . $eventstart  . "\", title: \"" . $title .  "\"}'";
                $markup .= $title . " />";
            }
            $count++;
        }
        $markup .= "</div>";
        
        $markup .= "<div id='calendar'></div>";

        return [
            '#type' => 'markup',
            '#markup' => $markup,
            '#attached' => [
                'library' => [
                  'spjcalendar/fullcal',
                ],
            ]
        ];
    }
}