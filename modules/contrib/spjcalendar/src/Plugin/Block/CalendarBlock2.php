<?php 
namespace Drupal\spjcalendar\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\UncacheableDependencyTrait;

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

        \Drupal::service('page_cache_kill_switch')->trigger();
        $tagString = null;
        $catString = null;
        if(\Drupal::request()->query->get('tag')){
            $tagString = \Drupal::request()->query->get('tag');
        }
        if(\Drupal::request()->query->get('category')){
            $catString = \Drupal::request()->query->get('category');
        }

        
        $calrss = "https://calendar.spjnetwork.org/feed.php?ex=";


        $markup = "";
        $max = 50;
        $count = 0;
        $filters = "tag:".$tagString . "|" . "category:" . $catString;
        $markup .= "<div class='inputs'>";
        $markup .= "</div>";
        if($tagString !== null || $catString !== null){
            /*
            $markup .= "<h3>Showing Calendar entries ";
            if($tagString !== null){
                $markup .= "tagged with '" . str_replace("_", " ",strtoupper($tagString)) . "'";
            }
            if($catString !== null){
                $markup .= "with the category '" . str_replace("_", " ",strtoupper($catString)) . "'";
            }
            $markup .="</h3><a href='/events'>Reset filter</a>";
            */
        }
        $markup .= "<div class='tabs'><nav class='tabs-wrapper tabs-primary is-collapsible'>
            <ul class='nav nav-tabs flex-column flex-md-row primary clearfix'>
                <li class='nav-item nav-link active'>
                    <a href='#tabCalendar'>Calendar View</a>
                </li>
                <li class='nav-item nav-link'>
                <a href='#tabList'>List View</a>
            </li>
            </ul>
            </nav></div>";
        $markup .= "<div class='tab-content'>
            <div id='tabCalendar' class='tab-pane active'>
                <div id='spjcalendar'></div>
            </div>
            <div id='tabList' class='tab-pane'><div id='spjcalendarlist'></div>";
        $markup .= "</div></div>";
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