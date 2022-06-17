<?php

namespace Drupal\svg_embed\Plugin\Filter;

use DOMXPath;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\svg_embed\SvgEmbedProcessInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to embed and translate SVG images.
 *
 * @Filter(
 *   id = "svg_embed",
 *   title = @Translation("Embed and translate SVG images"),
 *   description = @Translation("Allows to embed SVG graphics into text like
 *   with images and even translates text strings in the SVG file to the
 *   language of the node."), type =
 *   Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE
 * )
 */
class SvgEmbed extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * An entity manager object.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\svg_embed\SvgEmbedProcessInterface
   */
  protected $svgEmbedProcess;

  /**
   * Constructs a \Drupal\svg_embed\Plugin\Filter\SvgEmbed object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\svg_embed\SvgEmbedProcessInterface $svg_embed_process
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, SvgEmbedProcessInterface $svg_embed_process) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->svgEmbedProcess = $svg_embed_process;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('svg_embed.process')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);

    // Do we have at least one SVG reference in the $text?
    if (stripos($text, 'data-entity-type="file"') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new DOMXPath($dom);
      $processed_uuids = [];
      $patterns = [];

      // Identify the SVG nodes.
      /** @var \DOMNode $node */
      foreach ($xpath->query('//*[@data-entity-type="file" and @data-entity-uuid]') as $node) {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $uuid = $node->getAttribute('data-entity-uuid');
        // Only process the first occurrence of each file UUID.
        if (!isset($processed_uuids[$uuid])) {
          $processed_uuids[$uuid] = $this->svgEmbedProcess->translate($uuid, $langcode);
        }
        $patterns[(string) $node] = $uuid;
      }
      $text = Html::serialize($dom);
      foreach ($patterns as $pattern => $uuid) {
        $text = str_replace($pattern, $processed_uuids[$uuid], $text);
      }
      $result->setProcessedText($text);
    }

    return $result;
  }

}
