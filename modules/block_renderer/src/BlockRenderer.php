<?php

namespace Drupal\block_renderer;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Class BlockRenderer.
 */
class BlockRenderer {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * Drupal\Core\Block\BlockManagerInterface definition.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $pluginManagerBlock;
  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new BlockRenderer object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, BlockManagerInterface $plugin_manager_block, AccountProxyInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->pluginManagerBlock = $plugin_manager_block;
    $this->currentUser = $current_user;
  }

  /**
   * Renders content blocks.
   */
  public function renderContentBlock($id, array $config = []) {
    $content = [];
    $this->setDefaultAttributes($id, $config);
    $block = $this->entityTypeManager->getStorage('block_content')->load($id);
    if ($block) {
      $content = $this->entityTypeManager->getViewBuilder('block_content')->view($block);
    }

    return $this->theme($content, $config['#attributes']);
  }

  /**
   * Renders plugin blocks.
   */
  public function renderPluginBlock($id, array $config = []) {
    $content = [];
    $this->setDefaultAttributes($id, $config);
    $block = $this->pluginManagerBlock->createInstance($id, []);
    if ($block) {
      $access_result = $block->access($this->currentUser);
      // $access_result can be boolean or an AccessResult class.
      if (is_object($access_result) && $access_result->isAllowed() || is_bool($access_result) && $access_result) {
        // @todo Cache tags/contexts?
        $content = $block->build();
      }
    }

    return $this->theme($content, $config['#attributes']);
  }

  /**
   * Helper function to set default attributes.
   */
  private function setDefaultAttributes($id, array &$config) {
    $config['#attributes']['class'][] = 'block';
    $config['#attributes']['class'][] = Html::cleanCssIdentifier('block-' . $id);
  }

  /**
   * Theme the blocks.
   */
  private function theme($content, $attributes) {
    $build = [
      '#theme' => 'block_renderer',
      '#attributes' => [],
    ];

    // Bubble attributes to the top of the render array.
    if (isset($content['#attributes'])) {
      $build['#attributes'] += $content['#attributes'];
      unset($content['#attributes']);
    }

    // Add the content.
    $build['#content'] = $content;

    // Add custom classes.
    foreach ($attributes['class'] as $class) {
      $build['#attributes']['class'][] = $class;
    }

    return $build;
  }

}
