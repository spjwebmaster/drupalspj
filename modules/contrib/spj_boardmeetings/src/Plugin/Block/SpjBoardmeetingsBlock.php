<?php 
namespace Drupal\spj_boardmeetings\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Cache\UncacheableDependencyTrait;


/**
 * Provides a 'SPJ Board Meetings List' block.
 *
 * @Block(
 *  id = "spj_boardmeetings_block",
 *  label = "Previous SPJ Board Meetings List",
 *  admin_label = @Translation("SPJ Board Meetings List"),
 * )
 */
class SpjBoardmeetingsBlock extends BlockBase  {




    function build(){

        return [
            '#markup' => '<div id="boardMeetingList"></div>',
            '#attached' => [
                'library' => [
                  'spj_boardmeetings/board',
                ]
            ],
        ];
    }
}