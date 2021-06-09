<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Clean up urls before importing.
 *
 * @MigrateProcessPlugin(
 *   id = "process_image"
 * )
 */
class ProcessImageOut extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if ($value) {

      return str_replace('https://www.dcc.com/wp-content/uploads/sites/33/', '/sites/default/files/wp-thumbnails/',$value);

    }

  }

}

