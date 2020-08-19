<?php

namespace Drupal\eventtime\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Defines EventTimeController class.
 */
class EventTimeController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
    ];
  }
  
}