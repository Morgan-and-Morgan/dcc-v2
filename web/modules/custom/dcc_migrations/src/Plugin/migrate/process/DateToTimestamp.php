<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Date to timestamp conversion.
 *
 * @MigrateProcessPlugin(
 *   id = "date_to_timestamp"
 * )
 *
 * If the date is a string without delimiters we pass an argument to convert it
 * to a date. A source value of '19960101' transforms to 1996-01-01.
 */
class DateToTimestamp extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $fixDate = $this->configuration['fix_date'];

    if ($fixDate == TRUE) {
      return date("Y-m-d", strtotime($value));
    }
    else {
      return strtotime($value . ' UTC');
    }

  }

}
