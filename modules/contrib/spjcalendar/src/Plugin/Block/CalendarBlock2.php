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


        $tagString = null;
        $catString = null;
        if(\Drupal::request()->query->get('tag')){
            $tagString = \Drupal::request()->query->get('tag');
        }
        if(\Drupal::request()->query->get('category')){
            $catString = \Drupal::request()->query->get('category');
        }

        
        $calrss = "http://calendar.spjnetwork.org/feed.php?ex=";


        $markup = "";
        $max = 50;
        $count = 0;
        $filters = "tag:".$tagString . "|" . "category:" . $catString;
        $markup .= "<div class='inputs'>";
        $markup .= "<div class='calendar_filters' data-value='" . $filters  . "' ></div>";
        $markup .= "</div>";
        if($tagString !== null || $catString !== null){
            $markup .= "<h3>Showing Calendar entries ";
            if($tagString !== null){
                $markup .= "tagged with '" . str_replace("_", " ",strtoupper($tagString)) . "'";
            }
            if($catString !== null){
                $markup .= "with the category '" . str_replace("_", " ",strtoupper($catString)) . "'";
            }
            $markup .="</h3>";
        }
        $markup .= "<div id='spjcalendar'></div>";

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