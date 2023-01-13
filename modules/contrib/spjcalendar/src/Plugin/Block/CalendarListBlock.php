<?php 
namespace Drupal\spjcalendar\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Calendar List' block.
 *
 * @Block(
 *  id = "spj_calendar_list_block",
 *  label = "SPJ Calendar List",
 *  admin_label = @Translation("SPJ Calendar List"),
 * )
 */
class CalendarListBlock extends BlockBase  {


    /**
   * @return int
   */
    public function getCacheMaxAge() {
        return 0;
    }
   
  /**
   * {@inheritdoc}
   */
    public function build() {

        $params = \Drupal::request()->query->all();
        $tagString = null;
        $catString = null;
        if(\Drupal::request()->query->get('tag')){
            $tagString = \Drupal::request()->query->get('tag');
        }
        if(\Drupal::request()->query->get('category')){
            $catString = \Drupal::request()->query->get('category');
        }

        $calrss = "http://calendar.spjnetwork.org/feed.php?ex=";
        $feed = simplexml_load_file($calrss);

        $markup = "<div class='view spj_calendar_list'>";
        $max = 55;
        $count = 0;
        foreach ($feed->channel->item as $item) {
            if($count<$max){
                $title = (string) $item->title;
                $description = (string) $item->description;
                $eventstart = (string) $item->eventstart;
                $eventstarttime = (string) $item->eventstarttime;
                $eventstarttimepretty = (string) $item->eventstarttimepretty;
                $eventendtimepretty = (string) $item->eventendtimepretty;
                $eventend = (string) $item->eventend;
                $link = (string) $item->link;
                $tags = (string) $item->tags;
                $taglist = explode(",",$tags);
                $category = (string) $item->category;

                // check if there is a filter
                //tagString
                $processThisEntry = true;
                if($tagString!=null){
                    $processThisEntry = false;
                    foreach($taglist as $taggy){
                        $url = str_replace(" ", "_", trim($taggy));
                        $url = strtolower($url);
                        if($tagString==$url){
                            $processThisEntry =true;
                        }
                    }
                }
                if($catString!=null){
                    $processThisEntry = false;
                    

                    $catLink = trim(strtolower(str_replace(" ", "_", $category)));
                    if($catString==$catLink){
                        $processThisEntry =true;
                    }
                    
                }

                if($processThisEntry==true){

                $markup .= "<div class='views-row'><h3>" . $eventstarttimepretty . "</h3>";
                $markup .= "<h2><a href='" . $link . "' target='_blank' >" .  $title . "</a></h2>";
                $markup .= "<p>" . $eventstarttimepretty . " - " . $eventendtimepretty . "</p>";

                $catLink = trim(strtolower(str_replace(" ", "_", $category)));
                $markup .= "<strong>Category:</strong><br /> <a class='badge bg-primary text-white' href='?category=" .$catLink . "'>" . $category . "</a> <br /> ";
                $markup .= "<strong>Tags:</strong><br > "; 
                foreach($taglist as $taggy){
                    $url = str_replace(" ", "_", trim($taggy));
                    $url = strtolower($url);
                    $markup .= "<a href='?tag=" . $url. "' class='badge bg-success text-white mr-1'>" . $taggy . "</a>";
                }
                
                $markup .= "<br /><strong>Link:</strong><br /><a href='" . $link . "' target='_blank' ><strong>Click here</strong></a><br /><strong>Description:</strong><br /><div class='calendar-description'>";
                $markup .= $description . "</div></div>";
                }
            }
            $count++;
        }
        $markup .= "</div>";


        return [
            '#type' => 'markup',
            '#markup' => $markup,
            
        ];

    }
}