<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Clean up urls before importing.
 *
 * @MigrateProcessPlugin(
 *   id = "convert_http"
 * )
 */
class ConvertHttp extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Decoding html characters.
    $string = htmlspecialchars_decode($value);
    $pattern = '/(http:\/\/www.dcc.com\/wp-content\/uploads\/)/';
    $replacement = 'https://www.dcc.com/sites/default/files/wp-content/uploads/';
    $body = preg_replace($pattern, $replacement, $string);
    return $body;
  }

}
