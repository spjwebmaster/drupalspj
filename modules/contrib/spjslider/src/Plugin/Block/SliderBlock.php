<?php 
namespace Drupal\spjslider\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Provides a 'SPJ Slider' block.
 *
 * @Block(
 *  id = "spj_slider_block",
 *  label = "SPJ Slider (Swiper JS)",
 *  admin_label = @Translation("SPJ Slider"),
 * )
 */
class SliderBlock extends BlockBase  {

  /**
   * {@inheritdoc}
   */
    public function build() {

        $data = array();

        $current_url = Url::fromRoute('<current>');
        $path = $current_url->toString();

        $output = $path . "<br />";

        $paths = explode("/",$path);
        $last = $paths[count($paths)-1];
        

        $query = \Drupal::entityQuery('node');
        $query->condition('status', 1);
        $query->condition('type', 'slider_slide');
        $query->condition('field_page_path', $last);
        $ids = $query->execute();

        $nodes = Node::loadMultiple($ids);
        $isSingle = false;
        if(count($nodes)>0){
            foreach($nodes as $node){
                
                $body = $node->get('body')->value;
                $imageID = $node->get("field_slider_image")->target_id;
                
                $file = File::load($imageID);
                if($file){
                    $file_url = $file->createFileUrl();
                    $output .= "<div class='swiper-slide' style='background-image:url(" . $file_url . ")'>";
                    $output .= "<img src=\"" .$file_url ."\" alt='slider' />";

                    $output .= "<div class='swiper-content'>";
                    $output .= $body;
                    $output .= "</div>";
                    $output .= "</div>";
                    $temp = [
                        "image" => $file_url,
                        "body" => $body
                    ];
                    $data[] = $temp;
                }
            }
        
        } else {
            // get a default one from the previous URL path level
           
            $tryPath = $paths[count($paths)-2];

            $query = \Drupal::entityQuery('node');
            $query->condition('status', 1);
            $query->condition('type', 'slider_slide');
            $query->condition('field_page_path', $tryPath);
            $ids = $query->execute();
            $nodes = Node::loadMultiple($ids);

            if(count($nodes)>0){
                foreach($nodes as $node){
                    $body = $node->get('body')->value;
                    $imageID = $node->get("field_slider_image")->target_id;
                    
                    $file = File::load($imageID);
                    
                    $file_url = $file->createFileUrl();
                    $temp = [
                        "image" => $file_url,
                        "body" => $body
                    ];
                    $data[] = $temp;
                }

            } else {

                

                    // finally get a random header that is available (i.e has)
                    $query = \Drupal::entityQuery('node');
                    $query->condition('status', 1);
                    $query->condition('type', 'slider_slide');
                    //$query->isNull('field_page_path');
                    //$query->condition('field_page_path', null);
                    $query->condition('field_page_path', NULL, 'IS NULL');
                    $ids = $query->execute();

                    
                    if(count($ids)>0){
                        $id = array_rand($ids,1);
                        shuffle($ids);
                            
                        $body="";
                        $node_details = Node::load($ids[0]);
                        $imageID = $node_details->get("field_slider_image")->target_id;

                        $file = File::load($imageID);
                        
                        $file_url = $file->createFileUrl();
                        $temp = [
                            "image" => $file_url,
                            "body" => $body
                        ];
                        $data[] = $temp;
                        
                    } else {
                        // then I give up. Lorem Picum them.
                        $temp = [
                            "image" => "https://picsum.photos/1000/500",
                            "body" => ""
                        ];
                        $data[] = $temp;
                    }
                
            }
        }

        
        //$output .= print_r($ids, true);
        return [
            //'#type' => 'markup',
            '#markup' => $output,
            '#theme' => 'slider_block',
            '#single' => $isSingle,
            '#data' => $data,
            '#attached' => [
                'library' => [
                    'spjswiper/swiper',
                ],
            ]
        ];
    }
}