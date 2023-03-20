<?php

namespace Drupal\shortcode_basic_tags\Plugin\Shortcode;


use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\node\Entity\Node;

/**
 * Insert div or span around the text with some css classes.
 *
 * @Shortcode(
 *   id = "bio",
 *   title = @Translation("Bio"),
 *   description = @Translation("Insert a bio reference.")
 * )
 */
class BioShortcode extends ShortcodeBase {

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
          $email = $node->get("field_email")->value;
          $ret .= "<a href='mailto:" . $email . "'>" . $title . "</a>";
        }
              
     
      return $ret;

    }
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $output = [];
    $output[] = '<p><strong>' . $this->t('[bio id="1" (view="full") /]') . '</strong>';
    $output[] = $this->t('Inserts a bio.') . '</p>';
    if ($long) {
      $output[] = '<p>' . $this->t('The bio reference display view can be specified using the <em>view</em> parameter.') . '</p>';
    }

    return implode(' ', $output);
  }

}
