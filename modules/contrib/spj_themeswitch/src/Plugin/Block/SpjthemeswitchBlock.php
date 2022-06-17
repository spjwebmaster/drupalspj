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
            <a href=\"#\" data-theme='theme_light' class=\"btn active\"><i class=\"far fa-lightbulb\"></i></a>
            <a href=\"#\" data-theme='theme_default' class=\"btn\"><i class=\"fas fa-adjust\"></i></i></a>
            <a href=\"#\" data-theme='theme_dark'  class=\"btn\"><i class=\"far fa-moon\"></i></i></a>
        </div>";

        $markup = "<div class='btn-group'>
            <a href='#' data-theme='theme_light' class='btn btn-sm btn-outline-secondary'>Light</a>
            <a href='#' data-theme='theme_default' class='active btn btn-sm btn-secondary'>Default</a>
            <a href='#' data-theme='theme_dark' class='btn btn-sm btn-outline-secondary'>Dark</a>
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