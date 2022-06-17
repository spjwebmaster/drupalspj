<?php 
namespace Drupal\spj_toc\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;


/**
 * Provides a 'SPJ TOC' block.
 *
 * @Block(
 *  id = "spj_toc_block",
 *  label = "SPJ TOC",
 *  admin_label = @Translation("SPJ TOC"),
 * )
 */
class SpjTocBlock extends BlockBase  {

   
    public function build() {

       $markup = "What's on this page";
        return [
            '#type' => 'markup',
            '#markup' => $markup,
            '#attached' => [
                'library' => [
                    'spj_toc/spjtoc',
                ],
            ]
        ];
    }
}