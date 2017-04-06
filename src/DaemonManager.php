<?php

namespace Drupal\daemons;

use Drupal\Core\State\State;

/**
 * Manages daemons.
 */
class DaemonManager {

  /**
   * The State object.
   *
   * @var \Drupal\Core\State\State
   */
  private $state;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\State\State $state
   *   The state key-value store service.
   */
  public function __construct(State $state) {
    $this->state = $state;
  }

  /**
   * Execute task for daemon.
   */
  public function daemonExecute($task, $daemon) {
    switch ($task) {
      case 'start':
        $this->start($daemon);
        break;

      case 'stop':
        $this->stop($daemon);
        break;

      case 'restart':
        $this->restart($daemon);
        break;

      default:
    }
  }

  /**
   * Start daemon.
   */
  protected function start($daemon) {
    $module_path = drupal_get_path('module', 'daemons');
    $command = 'php ' . DRUPAL_ROOT . '/' . $module_path . '/daemon ' . $daemon . ' start >/dev/null &';
    exec($command);
  }

  /**
   * Stop daemon.
   */
  protected function stop($daemon) {
    $pid = $this->state->get($daemon);
    shell_exec("kill -9 $pid");
    $this->state->set($daemon, '');
  }

  /**
   * Restart daemon.
   */
  protected function restart($daemon) {
    $this->stop($daemon);
    usleep(300);
    $this->start($daemon);
  }

  /**
   * Check if the daemon is broken.
   *
   * @param int $pid
   *   The daemon pid.
   *
   * @return bool
   *   True if the daemon is broken.
   */
  public function isBroken($pid) {
    if (shell_exec("ps -p " . $pid . " | wc -l") > 1
      || file_exists("/proc/{$pid}")) {
      return FALSE;
    }

    return TRUE;
  }

}
