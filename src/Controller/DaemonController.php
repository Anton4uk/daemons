<?php

namespace Drupal\daemons\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides default daemon controller.
 *
 * @package Drupal\daemons\Controller
 */
class DaemonController extends ControllerBase {

  /**
   * Execute task for daemon.
   */
  public function run($task, $daemon) {
    \Drupal::service('daemon.manager')
      ->daemonExecute($task, $daemon);

    return $this->redirect('daemons.list');
  }

}
