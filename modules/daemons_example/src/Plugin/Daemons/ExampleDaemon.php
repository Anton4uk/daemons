<?php

namespace Drupal\daemons_example\Plugin\Daemons;

use Drupal\daemons\DaemonPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Mail\MailManagerInterface;

/**
 * Example daemon.
 *
 * @Daemon(
 *   id="example_daemon",
 *   label="Example daemon",
 *   periodicTimer="60",
 * )
 */
class ExampleDaemon extends DaemonPluginBase {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Constructs a new LittersUpdateQueueDaemon object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   A Guzzle client object.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   Mail manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, MailManagerInterface $mail_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function execute($loop) {
    try {
      $response = $this->httpClient->get('http://loripsum.net/api/1/plaintext');
    }
    catch (RequestException $e) {
      throw new \Exception('Error message: ' . $e->getMessage());
    }
    $params['message'] = $response->getBody();
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $this
      ->mailManager
      ->mail('daemons_example', 'daemons', NULL, $langcode, $params, NULL, TRUE);
  }

}
