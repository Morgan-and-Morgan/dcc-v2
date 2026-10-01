<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Clean up urls before importing.
 *
 * @MigrateProcessPlugin(
 *   id = "custom_explode"
 * )
 */
class CustomExplode extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Uses meta_value when it holds a value, and falls back to post_name
   * otherwise. $value[0] is the first value passed ('meta_value') and
   * $value[1] is the second ('post_name').
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if (strpos($value, ',') !== FALSE) {

      $item = explode(',', $value);

      $result = [];
      foreach ($item as $sub_value) {
        $result[] = ['fid' => $sub_value];
      }
      return $result;

    }
    else {

      $result[] = ['fid' => $value];

      return $result;
    }

  }

}
