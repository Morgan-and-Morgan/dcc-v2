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
   * We check if meta_value has a value if so we use it otherwise we use
   * post_name
   * $value[0] is the first value passed 'meta_value'
   * $value[1] is the second value passed 'post_name'
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
