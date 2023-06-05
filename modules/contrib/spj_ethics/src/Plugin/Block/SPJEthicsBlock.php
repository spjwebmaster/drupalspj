<?php 
namespace Drupal\spj_ethics\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SPJ Ethics Modal block.
 *
 * @Block(
 *  id = "spj_ethics_modal_block",
 *  label = "SPJ Ethics Modal",
 *  admin_label = @Translation("SPJ Ethics Modal"),
 * )
 */
class SPJEthicsBlock extends BlockBase  {

    function build(){

      
            $markup = '<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  ...
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>';
        
        return [
            '#markup' =>  $markup,
            '#attached' => [
                'library' => [
                  'spj_ethics/ethics',
                ]
            ],

        ];
    }

}