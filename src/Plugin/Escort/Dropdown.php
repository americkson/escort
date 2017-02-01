<?php

namespace Drupal\escort\Plugin\Escort;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a fallback plugin for missing block plugins.
 *
 * @Escort(
 *   id = "dropdown",
 *   admin_label = @Translation("Dropdown"),
 *   category = @Translation("Basic"),
 * )
 */
class Dropdown extends EscortPluginMultipleBase {

  /**
   * {@inheritdoc}
   */
  protected $provideMultiple = TRUE;

  /**
   * Whether the escort provides multiple sub-escorts.
   *
   * @var bool
   */
  protected $usesIcon = FALSE;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'trigger' => '',
      'trigger_icon' => '',
      'dropdown' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function escortForm($form, FormStateInterface $form_state) {
    $form['trigger'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Trigger title'),
      '#default_value' => $this->configuration['trigger'],
    ];
    if ($this->hasIconSupport()) {
      $form['trigger_icon'] = $this->escortIconForm($form, $form_state);
      $form['trigger_icon']['#title'] = $this->t('Trigger icon');
      $form['trigger_icon']['#default_value'] = $this->configuration['trigger_icon'];
    }
    $form['dropdown'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Dropdown content'),
      '#default_value' => $this->configuration['dropdown'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function escortSubmit($form, FormStateInterface $form_state) {
    $this->configuration['trigger'] = $form_state->getValue('trigger');
    $this->configuration['trigger_icon'] = $form_state->getValue('trigger_icon');
    $this->configuration['dropdown'] = $form_state->getValue('dropdown');
  }

  /**
   * {@inheritdoc}
   */
  public function buildItems() {
    $items = [];

    // Add a wrapper class.
    $items['#attributes']['class'][] = 'escort-dropdown';
    $items['#attached']['library'][] = 'escort/escort.dropdown';

    $items['link'] = $this->buildLink();
    $items['link']['#attributes']['class'][] = 'escort-dropdown-trigger';

    $items['dropdown'] = $this->buildDropdown();
    $items['dropdown']['#attributes']['class'][] = 'escort-dropdown-content';

    return $items;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildLink() {
    return [
      '#tag' => 'a',
      '#markup' => $this->configuration['trigger'],
      '#icon' => $this->configuration['trigger_icon'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function buildDropdown() {
    return [
      '#markup' => $this->configuration['dropdown'],
    ];
  }

}
