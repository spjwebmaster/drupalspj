<?php

namespace Drupal\spj_themeswitch\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Provides a 'SPJ Themeswitch' Block.
 *
 * @Block(
 *   id = "spj_themeswitch_block",
 *   admin_label = @Translation("SPJ ThemeSwitcher Block"),
 *   category = @Translation("Custom"),
 * )
 */


class SpjthemeswitchBlock extends BlockBase {

    public function build() {

        $markupSimple = "<div class=\"themeswitcher btn-group\">
            <a href=\"#\" data-theme='theme_default' class=\"btn\"><i class=\"fas fa-sun\"></i></i></a>
            <a href=\"#\" data-theme='theme_dark'  class=\"btn\"><i class=\"far fa-moon\"></i></i></a>
        </div>";
        return [
            '#markup' => $this->t($markupSimple),
            '#attached' => [
                'library' => [
                  'spj_themeswitch/themeswitcherjs',
                ]
            ],
        ];
    }
}