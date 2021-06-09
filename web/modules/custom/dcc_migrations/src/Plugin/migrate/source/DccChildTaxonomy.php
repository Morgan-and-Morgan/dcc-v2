<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for class action.
 *
 * @MigrateSource(
 *   id = "dcc_taxonomy"
 * )
 */
class DccChildTaxonomy extends SqlBase {

  /**
   * -- Get all class action taxonomies
   * -- class_type is only used in class action hence class_type = class action for this query
   * SELECT
   * wtt.term_id, taxonomy, count, name
   * FROM
   * wp_33_term_taxonomy wtt
   * INNER JOIN wp_33_terms wt
   * ON wtt.term_taxonomy_id = wt.term_id
   * WHERE
   * taxonomy = 'product_category';
   */

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('wp_33_term_taxonomy', 'wtt');
    $query->innerJoin('wp_33_terms', 'wt', 'wtt.term_taxonomy_id = wt.term_id');
    $query->condition('wtt.taxonomy', 'product_category', '=');
    $query->fields('wt', [
      'term_id',
      'name',
    ]);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'term_id' => $this->t('Term ID'),
      'name' => $this->t('Name'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'term_id' => [
        'type' => 'integer',
        'alias' => 'wt',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    return parent::prepareRow($row);
  }

}
