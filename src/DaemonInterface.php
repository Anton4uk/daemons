<?php

namespace Drupal\daemons;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines a daemon interface.
 *
 * @see plugin_api
 */
interface DaemonInterface extends PluginInspectionInterface {

  /**
   * Get plugin id.
   */
  public function getId();

  /**
   * Get daemon name.
   */
  public function getLabel();

  /**
   * Get periodic timer value.
   */
  public function getPeriodicTimer();

  /**
   * Get daemon status.
   */
  public function getStatus();

  /**
   * Get daemon pid.
   */
  public function getProcessId();

  /**
   * Set daemon pid.
   *
   * @param string $pid
   *   Php process id.
   */
  public function setProcessId($pid);

  /**
   * Execute daemon code.
   *
   * @param object $loop
   *   The react event loop.
   */
  public function execute($loop);

}
