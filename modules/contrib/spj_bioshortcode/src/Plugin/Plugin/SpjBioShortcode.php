<?php 
namespace Drupal\spj_bioshortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\node\Entity\Node;

/**
 * The image shortcode.
 *
 * @Shortcode(
 *   id = "spjbio",
 *   title = @Translation("SPj Bio Shortcode"),
 *   description = @Translation("Insert a reference to a bio entity.")
 * )
 */
class SpjbioShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

      // Merge with default attributes.
      $attributes = $this->getAttributes([
        'id' => '',
        'view' => 'full',
      ],
        $attributes
      );
  
  
  
  
      if ($attributes['id']) {
  
        $thistid = $attributes['id'];
        $query = \Drupal::entityQuery('node');
                  $query->condition('type', "bio");
                  $query->condition('field_role.entity.tid',$thistid);
          $bios = $query->execute();
        
          $ret = "";
          foreach($bios as $bio){
            $node = Node::load($bio);
            $title= $node->get("title")->value;
            $ret .= $title;
          }
                
       
        return $ret;
  
      }
    }
  
    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
      $output = [];
      $output[] = '<p><strong>' . $this->t('[spjbio id="1" (view="full") /]') . '</strong>';
      $output[] = $this->t('Inserts a bio.') . '</p>';
      if ($long) {
        $output[] = '<p>' . $this->t('The bio reference display view can be specified using the <em>view</em> parameter.') . '</p>';
      }
  
      return implode(' ', $output);
    }

  }
  