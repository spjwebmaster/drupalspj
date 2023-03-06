<?php 
namespace Drupal\spj_dog\Plugin\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'SPJ Day of Giving' block.
 *
 * @Block(
 *  id = "spj_dog_block",
 *  label = "Showing Spj Day of Giving Leaderboard",
 *  admin_label = @Translation("SPJ DOG Block"),
 * )
 */
class SpjDogBlock extends BlockBase  {
    function build(){

        $markup = '
        <div id="dogLeaderboardWrapper">
            
            
            <div class="searchWrapper">
                <input class="search" placeholder="Search" />
                <button class="searchClear">X</button>
            </div>
            
            
            <table class="table" id="myTable">
                <thead>
                  <tr>
                  <th class="col">
                    <button class="sort" data-sort="name">
                        Name
                      </button>
        
                  </th>
                  <th class="col">
                    <button class="sort" data-sort="donation">
                        Donation
                      </button>
                  </th>
                  <th class="col">
                    <button class="sort" data-sort="code">
                        Code
                      </button>
        
                  </th>
                </tr>
              </thead>
              <tbody id="leaderboardDOG" class="list">
                
        
                </tbody>
              </table>
          
          </div>';


        return [
            '#markup' => $markup,
            '#attached' => [
                'library' => [
                  'spj_dog/leaderboard',
                ]
            ],
        ];
    }
}