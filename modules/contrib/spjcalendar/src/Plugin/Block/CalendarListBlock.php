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
   * {@inheritdoc}
   */
    public function build() {

        
        $markup = "<div class='view spj_calendar_list item-list'><ul class='item-list'></ul></div>";

        return [
            '#type' => 'markup',
            '#markup' => $markup,
            '#attached' => [
                'library' => [
                  'spjcalendar/listCal',
                ],
            ]
            
        ];

    }
}