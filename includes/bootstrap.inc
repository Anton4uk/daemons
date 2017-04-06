<?php

/**
 * @file
 * Drupal kernel load.
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal.
$path = get_file_path(__DIR__ . '/', 'autoload.php', $result);
$autoloader = require_once $path;

// Drupal kernel boot.
$request = Request::createFromGlobals();
$kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->boot();
$kernel->prepareLegacyRequest($request);

// Initialise an anonymous session. required for the bootstrap.
\Drupal::service('session_manager')->start();

/**
 * Find path by file name.
 *
 * @param string $path
 *    The initial directory path fo search.
 * @param string $file_name
 *    The name of file for search.
 * @param string $result
 *    Temporary variable for result.
 *
 * @return string
 *    Full path to the searching file.
 */
function get_file_path($path, $file_name, &$result) {
  foreach (new \DirectoryIterator($path) as $fileInfo) {
    if ($fileInfo->isDot()) {
      continue;
    }
    if ($fileInfo->getFilename() == $file_name) {
      $result = $path . $fileInfo->getFilename();
      break;
    }
  }
  if (empty($result)) {
    get_file_path($path . '../', $file_name, $result);
  }

  return $result;
}
