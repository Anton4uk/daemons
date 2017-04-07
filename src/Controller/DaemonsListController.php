<?php

namespace Drupal\daemons\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Provide list of daemons.
 *
 * @package Drupal\daemons\Controller
 */
class DaemonsListController extends ControllerBase {

  /**
   * List of daemons.
   */
  public function daemonsList() {
    $list = [];
    $list['#type'] = 'container';
    $list['daemons'] = [
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#rows' => $this->buildRows(),
      '#empty' => '',
      '#attributes' => [
        'id' => 'daemons-list',
        'class' => ['daemons-list'],
      ],
    ];

    return $list;
  }

  /**
   * Build header.
   */
  protected function buildHeader() {
    return [
      $this->t('Name'),
      $this->t('Status'),
      $this->t('Pid'),
      $this->t('Operations'),
    ];
  }

  /**
   * Prepare list of daemons.
   */
  protected function buildRows() {
    $rows = [];
    // Get all existing daemon plugins.
    $plugin_service = \Drupal::service('plugin.manager.daemon');
    foreach ($plugin_service->getDefinitions() as $plugin_id => $plugin) {
      $instance = $plugin_service->createInstance($plugin_id);

      $row['title']['data']['#markup'] = $instance->getLabel();
      $row['status']['data']['#markup'] = $instance->getStatus();
      $row['pid']['data']['#markup'] = $instance->getProcessId();

      // Build operation start/stop for daemon.
      $row['operations']['data'] = [
        '#type' => 'operations',
        '#links' => [
          'daemon_start' => [
            'title' => $this->t('Start'),
            'url' => Url::fromRoute('daemon.run', [
              'task' => 'start',
              'daemon' => $instance->getId(),
            ]),
          ],
          'daemon_stop' => [
            'title' => $this->t('Stop'),
            'url' => Url::fromRoute('daemon.run', [
              'task' => 'stop',
              'daemon' => $instance->getId(),
            ]),
          ],
          'daemon_restart' => [
            'title' => $this->t('Restart'),
            'url' => Url::fromRoute('daemon.run', [
              'task' => 'restart',
              'daemon' => $instance->getId(),
            ]),
          ],
        ],
      ];

      $rows[] = $row;
    }

    return $rows;
  }

}
