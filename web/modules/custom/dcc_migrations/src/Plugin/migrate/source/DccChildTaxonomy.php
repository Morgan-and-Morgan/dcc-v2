<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for class action.
 *
 * @MigrateSource(
 *   id = "dcc_taxonomy"
 * )
 */
class DccChildTaxonomy extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * Selects term_id, taxonomy, count and name from wp_33_term_taxonomy
   * inner-joined to wp_33_terms on term_taxonomy_id = term_id, restricted to
   * the 'product_category' taxonomy.
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

}
