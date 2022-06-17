<?php

namespace Drupal\flickity;

use Drupal\Core\Render\Element\RenderCallbackInterface;

/**
 * Provides a trusted callback to alter the flickity output.
 *
 * @see theme_flickity()
 */
class FlickityBuilder implements RenderCallbackInterface {

  /**
   * Sets the flickity - #pre_render callback.
   */
  public static function preRender(array $element) {
    $items = array();
    foreach ($element['#output']['items'] as $key => $item) {
      $items[] = array(
        '#theme' => 'flickity_item',
        '#item' => $item['row'],
        '#attributes' => array(
          'class' => array('gallery-cell', 'item-' . $key)
        )
      );
    }

    // Build wrapper with Flickity items.
    $build = flickity_build($element['#output']['settings'], $items);

    // Provide pre render alter.
    \Drupal::moduleHandler()->alter('pre_render', $build);

    // Render the complete Flickity element.
    $element['#children'] = \Drupal::service('renderer')->render($build);

    return $element;
  }

}
