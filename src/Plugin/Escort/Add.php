<?php

namespace Drupal\escort\Plugin\Escort;

use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;

/**
 * Defines a fallback plugin for missing block plugins.
 *
 * @Escort(
 *   id = "add",
 *   admin_label = @Translation("Add"),
 *   no_ui = TRUE,
 * )
 */
class Add extends EscortPluginBase implements EscortPluginImmediateInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'region' => NULL,
      'icon' => 'fa-plus',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#tag'] = 'a';
    $build['#attributes'] = [
      'class' => ['use-ajax'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => Json::encode([
        'width' => 700,
      ]),
    ];
    $build['#attributes']['href'] = Url::fromRoute('escort.escort_library', [], [
      'query' => [
        'region' => $this->configuration['region'],
      ],
    ])->toString();
    $build['#attributes']['class'][] = 'escort-add';
    return $build;
  }

}
