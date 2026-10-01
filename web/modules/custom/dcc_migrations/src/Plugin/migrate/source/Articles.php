<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for class action articles.
 *
 * @MigrateSource(
 *   id = "articles"
 * )
 */
class Articles extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * Selects ID, post_author, post_date, post_content, post_title and
   * post_excerpt from wp_33_posts, restricted to rows whose post_type is
   * 'post'.
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

}
