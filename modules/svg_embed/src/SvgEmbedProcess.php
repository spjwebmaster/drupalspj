<?php

namespace Drupal\svg_embed;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\file\Entity\File;
use SimpleXMLElement;

/**
 * Class SvgEmbedProcess.
 *
 * @package Drupal\svg_embed
 */
class SvgEmbedProcess implements SvgEmbedProcessInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * SvgEmbedProcess constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, Connection $connection) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public function translate($uuid, $langcode): string {
    $xml = $this->loadFile($uuid);

    if ($this->moduleHandler->moduleExists('locale')) {
      // Go through the DOM and translate all relevant strings.
      $this->embedTranslate($xml, $langcode);
    }

    // Strip early comments and a potential xml tag.
    $svg = $xml->asXML();
    $svg_tag = strpos($svg, '<svg');
    return substr($svg, $svg_tag);
  }

  /**
   * @param string $uuid
   * @return \SimpleXMLElement
   */
  private function loadFile($uuid): SimpleXMLElement {
    $text = '';
    try {
      /** @var File $file */
      if ($file = $this->entityTypeManager
        ->getStorage('file')
        ->loadByProperties(['uuid' => $uuid])) {
        $text = file_get_contents($file->getFileUri());
      }
    }
    catch (InvalidPluginDefinitionException $e) {
      // TODO: log this exception.
    }
    catch (PluginNotFoundException $e) {
      // TODO: log this exception.
    }
    return new SimpleXMLElement($text);
  }

  /**
   * Helper function called recursively to translate all strings in an SVG file.
   *
   * @param SimpleXMLElement $xml
   *   the SVG graphic code
   * @param string $langcode
   *   the language code to which we need to translate
   */
  protected function embedTranslate($xml, $langcode): void {
    foreach ($xml as $child) {
      $this->embedTranslate($child, $langcode);
      if (isset($child->text) || isset($child->tspan)) {
        if (isset($child->text->tspan)) {
          $text = $child->text->tspan;
        }
        /** @noinspection NotOptimalIfConditionsInspection */
        elseif (isset($child->tspan)) {
          $text = $child->tspan;
        }
        else {
          $text = $child->text;
        }
        $i = 0;
        while (TRUE) {
          $string = (string) $text[$i];
          if (empty($string)) {
            break;
          }
          $string = trim($string);
          if (!empty($string)) {
            $query = $this->connection->select('locales_source', 's');
            $query->leftJoin('locales_target', 't', 's.lid = t.lid');
            /** @noinspection NullPointerExceptionInspection */
            $translation = $query->fields('t', ['translation'])
              ->condition('s.source', $string)
              ->condition('s.textgroup', 'svg_embed')
              ->condition('t.language', $langcode)
              ->execute()
              ->fetchField();
            $text[$i][0] = empty($translation) ? $string : $translation;
          }
          $i++;
        }
      }
    }
  }

}
