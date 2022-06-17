<?php

namespace Drupal\svg_embed;

/**
 * Interface SvgEmbedProcessInterface.
 *
 * @package Drupal\svg_embed
 */
interface SvgEmbedProcessInterface {

  /**
   * @param string $uuid
   * @param string $langcode
   * @return string
   */
  public function translate($uuid, $langcode): string;

}
