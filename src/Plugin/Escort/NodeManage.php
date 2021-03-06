<?php

namespace Drupal\escort\Plugin\Escort;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\micon\MiconIconize;

/**
 * Defines a plugin for managing content.
 *
 * @Escort(
 *   id = "node_manage",
 *   admin_label = @Translation("Node Manage"),
 *   category = @Translation("Node"),
 * )
 */
class NodeManage extends Aside implements ContainerFactoryPluginInterface {

  /**
   * The entity type.
   *
   * @var string
   */
  protected $entityType = 'node';

  /**
   * The entity type bundle.
   *
   * @var string
   */
  protected $entityTypeBundle = 'node_type';

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Manages a LocalTasksEscort instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
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
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'text' => $this->t('Manage [TYPE]'),
      'icon' => 'fa-edit',
      'bundle' => '',
    ) + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  protected function escortAccess(AccountInterface $account) {

    if ($account->hasPermission('administer content types')) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    if ($bundle = $this->configuration['bundle']) {
      if ($account->hasPermission("edit any $bundle content") || $account->hasPermission("edit own $bundle content")) {
        return AccessResult::allowed()->cachePerPermissions();
      }
    }

    // No opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  public function escortForm($form, FormStateInterface $form_state) {
    $form = parent::escortForm($form, $form_state);
    $options = [];
    foreach ($this->entityTypeManager->getStorage($this->entityTypeBundle)->loadMultiple() as $entity) {
      $options[$entity->id()] = MiconIconize::iconize($entity->label())->addMatchPrefix('content_type');
    }
    $form['bundle'] = [
      '#type' => 'radios',
      '#title' => $this->t('Bundle'),
      '#description' => $this->t('If no bundle is selected, all bundles will be available.'),
      '#options' => $options,
      '#default_value' => $this->configuration['bundle'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function escortSubmit($form, FormStateInterface $form_state) {
    parent::escortSubmit($form, $form_state);
    $this->configuration['bundle'] = $form_state->getValue('bundle');
  }

  /**
   * {@inheritdoc}
   */
  protected function escortBuildAsideContent() {
    $build = [];
    $build['list'] = $this->entityTypeManager->getHandler($this->entityType, 'escort_list_builder')->setTypes([$this->configuration['bundle']])->render();
    return $build;
  }

}
