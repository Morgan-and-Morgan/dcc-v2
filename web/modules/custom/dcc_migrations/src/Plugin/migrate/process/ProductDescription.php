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
 *   id = "product_description"
 * )
 * @codingStandardsIgnoreStart
 *
 *
 * @codingStandardsIgnoreEnd
 */
class ProductDescription extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (isset($value)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_posts', 'p', ['target' => 'migrate']);
      $query->leftJoin('wp_33_postmeta', 'meta', 'meta.post_id = p.id');
      $query->condition('p.post_type', 'product');
      $query->condition('p.post_status', 'publish');
      $orGroup1 = $query->orConditionGroup()
        ->condition('meta.meta_key', 'description')
        ->condition('meta.meta_key', 'drug_description');
      $query->condition($orGroup1);
      $query->condition('p.id', $value);
      $query->groupBy('p.id');
      $query->addField('p', 'id', 'id');
      $query->addField('meta', 'meta_value', 'desc');
      $result = $query->execute()->fetchAll();
      return $result[0]->desc;
    } else {
      return NULL;
    }

  }

}
