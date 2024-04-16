<?php

namespace Drupal\rsvp_form_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom form block.
 *
 * @Block(
 *   id = "rsvp_form_data",
 *   admin_label = @Translation("RSVP User Form"),
 *   category = @Translation("Custom")
 * )
 */
class CustomFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\rsvp_form_data\Form\CustomFormBlockForm');
    return $form;
  }

}