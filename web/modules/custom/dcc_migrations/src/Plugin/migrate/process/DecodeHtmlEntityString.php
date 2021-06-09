<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Decode HTML entities.
 *
 * @MigrateProcessPlugin(
 *   id = "decode_html_entities"
 * )
 */
class DecodeHtmlEntityString extends ProcessPluginBase {

  /**
   * Decode html special characters.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return htmlspecialchars_decode($value, ENT_QUOTES);
  }

}
