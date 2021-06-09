<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Add prefix to URL aliases.
 *
 * @MigrateProcessPlugin(
 *   id = "add_url_alias_prefix"
 * )
 */
class AddUrlAliasPrefix extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $prefix = !empty($this->configuration['prefix']) ? '/' . $this->configuration['prefix'] : '';
    return $prefix . $value;
  }

}
