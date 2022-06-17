<?php

/**
 * @file
 * flickity.theme.inc
 */

use Drupal\Core\Template\Attribute;
use Drupal\flickity\FlickityBuilder;

/**
 * Template preprocess Flickity variables.
 */
function template_preprocess_flickity(&$variables) {
  // Load Flickity settings.
  $group = flickity_settings($variables['settings']);

  // Attach Flickity library.
  $variables['#attached'] = array(
    'drupalSettings' => array(
      'flickity' => array(
        $group['id'] => array(
          'settings' => $group['settings']
        )
      )
    ),
    'library' => array(
      'flickity/local',
      'flickity/settings'
    )
  );
}

/**
 * Theme declaration for Flickity.
 */
function theme_flickity($variables) {
  $output = array(
    '#output' => array(
      'settings' => $variables['settings'],
      'items' => $variables['items']
    ),
    '#pre_render' => [
      [FlickityBuilder::class, 'preRender']
    ],
  );

  return \Drupal::service('renderer')->render($output);
}

/**
 * Template preprocess Flickity item variables.
 */
function template_preprocess_flickity_item(&$variables) {
  $variables['attributes'] = new Attribute($variables['attributes']);
}

/**
 * Returns the render array for Flickity wrapper.
 */
function flickity_build($group, $output) {
  return array(
    '#output' => $output,
    '#attributes' => array('class' => array($group, 'flickity')),
    '#theme_wrappers' => array('flickity_wrapper')
  );
}

/**
 * Template preprocess Flickity wrapper variables.
 */
function template_preprocess_flickity_wrapper(&$variables) {
  $variables['attributes'] = new Attribute($variables['attributes']);
}
