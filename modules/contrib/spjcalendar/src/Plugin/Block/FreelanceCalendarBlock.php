<?php 
namespace Drupal\spjcalendar\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Freelance Calendar' block.
 *
 * @Block(
 *  id = "spj_freelance_calendar_block",
 *  label = "SPJ Freelance Calendar",
 *  admin_label = @Translation("SPJ Freelance Calendar"),
 * )
 */
class FreelanceCalendarBlock extends BlockBase  {


    public function build() {

        $calrss = "http://feeds.feedburner.com/spjcalfreelance";
    
        $data["data"] = readRSS($calrss, 5);
        //dpm($data);
        return [
                '#theme' => 'spj_calendar_block',
                '#data' => $data
        
        ];
    }
}