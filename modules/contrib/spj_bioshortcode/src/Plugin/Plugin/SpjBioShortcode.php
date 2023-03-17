<?php 
namespace Drupal\spj_bioshortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * The image shortcode.
 *
 * @Shortcode(
 *   id = "spjbio",
 *   title = @Translation("SPj Bio Shortcode"),
 *   description = @Translation("Insert a reference to a bio entity.")
 * )
 */
class SpjBioShortcode extends ShortcodeBase {

    /**
     * {@inheritdoc}
     */
    public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
  
      // Merge with default attributes.
      $attributes = $this->getAttributes([
        'path' => '<front>',
        'url' => '',
        'title' => '',
        'class' => '',
        'id' => '',
        'style' => '',
        'media_file_url' => FALSE,
      ],
        $attributes
      );
      $url = $attributes['url'];
      if (empty($url)) {
        $url = $this->getUrlFromPath($attributes['path'], $attributes['media_file_url']);
      }
      $title = $this->getTitleFromAttributes($attributes['title'], $text);
      $class = $this->addClass($attributes['class'], 'button');
  
      // Build element attributes to be used in twig.
      $element_attributes = [
        'href' => $url,
        'class' => $class,
        'id' => $attributes['id'],
        'style' => $attributes['style'],
        'title' => $title,
      ];
  
      // Filter away empty attributes.
      $element_attributes = array_filter($element_attributes);
  
      $output = [
        '#url' => $url,
        '#attributes' => $element_attributes,
        '#text' => $text,
      ];
  
      return $this->render($output);
    }
  
    /**
     * {@inheritdoc}
     */
    public function tips($long = FALSE) {
      $output = [];
      $output[] = '<p><strong>' . $this->t('[bio path="path" (class="additional class")]text[/bio]') . '</strong> ';
      if ($long) {
        $output[] = $this->t('Inserts a link formatted like as a button. The <em>path</em> parameter provides the link target (the default is the front page).
      The <em>title</em> will be formatted as a link title (small tooltip over the link - helps for SEO).
      Additional class names can be added by the <em>class</em> parameter.') . '</p>';
      }
      else {
        $output[] = $this->t('Inserts the contents of a bio entity.') . '</p>';
      }
      return implode(' ', $output);
    }
  
  }
  