<?php

namespace Drupal\daemons;

use Drupal\Component\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Defines a daemon plugin base class.
 *
 * @see \Drupal\daemons\DaemonInterface
 */
abstract class DaemonPluginBase extends PluginBase implements DaemonInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * Get plugin id.
   */
  public function getId() {
    return $this->pluginDefinition['id'];
  }

  /**
   * Get daemon name.
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * Get periodic timer value.
   */
  public function getPeriodicTimer() {
    $definition = $this->pluginDefinition;
    return isset($definition['periodicTimer']) ? $definition['periodicTimer'] : '';
  }

  /**
   * Get daemon status.
   */
  public function getStatus() {
    $pid = \Drupal::state()->get($this->getId());
    if ($pid) {
      $daemon_manager = \Drupal::service('daemon.manager');
      return $daemon_manager->isBroken($pid) ? 0 : 1;
    }

    return 0;
  }

  /**
   * Get daemon pid.
   */
  public function getProcessId() {
    return \Drupal::state()->get($this->getId());
  }

  /**
   * Set daemon pid.
   *
   * @param string $pid
   *   Php process id.
   */
  public function setProcessId($pid) {
    \Drupal::state()->set($this->getId(), $pid);
  }

  /**
   * {@inheritdoc}
   */
  abstract public function execute($loop);

}
