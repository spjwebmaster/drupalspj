<?php 
namespace Drupal\spjcalendar\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Events' block.
 *
 * @Block(
 *  id = "spj_calendar_block",
 *  label = "SPJ Calendar",
 *  admin_label = @Translation("SPJ Calendar"),
 * )
 */
class CalendarBlock extends BlockBase  {

   
    public function build() {

        $calrss = "http://calendar.spjnetwork.org/feed.php?ex=";
        $feed = simplexml_load_file($calrss);

        $markup = "";
        $max = 15;
        $count = 0;
        $markup .= "<div class='swiper multi'><div class='swiper-wrapper'>";
        foreach ($feed->channel->item as $item) {
            if($count<$max){
                $title = (string) $item->title;
                $description = (string) $item->description;
                $eventstart = (string) $item->eventstart;
                $eventend = (string) $item->eventend;
                $link = (string) $item->link;
                $eventstarttimepretty = (string) $item->eventstarttimepretty;
                $eventendtimepretty = (string) $item->eventendtimepretty;

                $markup .= "<div class='swiper-slide'>";
                $markup .= "<a href='" . $link . "' target='_blank' ><strong>" . $eventstart  . "</strong></a><br />";
                $markup .= "<h3><a href='" . $link . "' target='_blank' >" . $title . "</a></h3>";
                $markup .= "<span class='time'>" . $eventstarttimepretty . " - " . $eventendtimepretty . "</p>";
                //$markup .="<span class='calendar-description'><small>" . $description . "</small></span>";
                $markup .="</div>";
            }
            $count++;
        }
        $markup .= "</div>";
        $markup .= "<div class=\"swiper-pagination\"></div>";
        $markup .= "<div class=\"swiper-button-prev\"></div>";
        $markup .= "<div class=\"swiper-button-next\"></div>";
        $markup .= "</div>";

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