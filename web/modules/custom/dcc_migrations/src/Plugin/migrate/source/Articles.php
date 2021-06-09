<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for class action articles.
 *
 * @MigrateSource(
 *   id = "articles"
 * )
 */
class Articles extends SqlBase {

  /**
   * -- Get all class action taxonomies
   * -- class_type is only used in class action hence class_type = class action for this query
   * SELECT
   * wpp.ID, post_author, post_date, post_content, post_title, post_excerpt
   * FROM
   * wp_33_posts wpp
   * WHERE
   * post_type = 'post';
   */

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('wp_33_posts', 'wpp');
    $query->condition('wpp.post_type', 'post', '=');
    $query->fields('wpp', [
      'ID',
      'post_author',
      'post_date',
      'post_content',
      'post_title',
    ]);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'ID' => $this->t('ID'),
      'post_author' => $this->t('Post Author'),
      'post_date' => $this->t('Post Date'),
      'post_content' => $this->t('Post Content'),
      'post_title' => $this->t('Post Title'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'ID' => [
        'type' => 'integer',
        'alias' => 'wpp',
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
