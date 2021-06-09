<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Apply the automatic paragraph filter to content.
 *
 * @MigrateProcessPlugin(
 *   id = "alt_by_image_id"
 * )
 * @codingStandardsIgnoreStart
 *
 *
 * @codingStandardsIgnoreEnd
 */
class AltByImageId extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (isset($value)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_postmeta', 'pm', ['target' => 'migrate']);
      $query->condition('pm.post_id', $value);
      $query->condition('pm.meta_key', '_wp_attachment_image_alt');
      $query->addField('pm', 'meta_value', 'alt');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $alt = $item->alt;
      }
      return $alt;
    }
    else {
      return NULL;
    }
  }
}
