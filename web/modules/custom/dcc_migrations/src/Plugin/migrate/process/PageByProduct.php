<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Return Page id by product Id
 *
 * @MigrateProcessPlugin(
 *   id = "page_id_by_product"
 * )
 * @codingStandardsIgnoreStart
 *
 *
 * @codingStandardsIgnoreEnd
 */
class PageByProduct extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (isset($value)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_p2p', 'p2p', ['target' => 'migrate']);
      $query->condition('p2p.p2p_to', $value);
      $query->condition('p2p.p2p_type', 'page_to_product');
      $query->addField('p2p', 'p2p_from', 'page_id');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $page_id = $item->page_id;
      }
      return $page_id;
    }
    else {
      return NULL;
    }
  }
}
