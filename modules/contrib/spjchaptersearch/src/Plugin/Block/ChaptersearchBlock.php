<?php

namespace Drupal\spjchaptersearch\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Provides a 'SPJChaptersearch' Block.
 *
 * @Block(
 *   id = "spjchaptersearch_block",
 *   admin_label = @Translation("Chapter Search Block"),
 *   category = @Translation("Custom"),
 * )
 */


class SPJChaptersearchBlock extends BlockBase {

    public function build() {

        $markup = "<div class='chaptersearch_wrapper'><div class='row-no-gutter'>
            <div class='col-sm-4'>Chapter Search</div>
        </div></div>";
        return [
            '#markup' => $this->t($markup),
            '#attached' => [
                'library' => [
                  'chaptersearch/chaptersearch',
                ]
            ],
        ];
    }
}