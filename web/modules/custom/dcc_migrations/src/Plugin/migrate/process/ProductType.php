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
 *   id = "product_type"
 * )
 * @codingStandardsIgnoreStart
 *
 *
 * @codingStandardsIgnoreEnd
 */
class ProductType extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (isset($value)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_posts', 'p', ['target' => 'migrate']);
      $query->leftJoin('wp_33_term_relationships', 'wtr', 'wtr.object_id = p.id');
      $query->leftJoin('wp_33_term_taxonomy', 'wtt', 'wtr.term_taxonomy_id = wtt.term_id');
      $query->condition('p.post_type', 'product');
      $query->condition('p.post_status', 'publish');
      $query->condition('wtt.taxonomy', 'product_type');
      $query->condition('p.id', $value);
      $query->addExpression('GROUP_CONCAT(wtr.term_taxonomy_id)', 'category');
      $query->groupBy('p.id');
      $query->addField('p', 'id', 'id');
      $query->addField('wtt', 'term_id', 'term_id');
      $result = $query->execute()->fetchAll();
      $arr = (object) $result;
      foreach ($arr as $item) {
        $product_types = $item->category;
      }
      return $product_types;
    }
    else {
      return NULL;
    }
  }
}
