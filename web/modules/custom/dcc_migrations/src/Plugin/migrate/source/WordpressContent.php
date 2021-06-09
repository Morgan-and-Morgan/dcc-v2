<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

// Use Drupal\dcc_migrations\Plugin\migrate\source\MySqlBase;.
use Drupal\migrate\Row;

/**
 * Extract content from Wordpress site.
 *
 * @MigrateSource(
 *   id = "wordpress_content"
 * )
 */
class WordpressContent extends MySqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $prefix = $this->getPrefix();
    $query = $this->select($prefix . '_posts', 'p');
    $query
      ->fields('p', [
        'id',
        'post_author',
        'post_date',
        'post_title',
        'post_content',
        'post_excerpt',
        'post_modified',
        'post_name',
      ]);
    $query->condition('p.post_status', 'publish');
//    $query->join('wp_33_term_relationships', 'tr', 'tr.object_Id = p.ID');
//    $query
//      ->fields('tr', [
//        'term_taxonomy_id',
//      ]);
//    $query->join('wp_33_terms', 'trm', 'trm.term_id = tr.term_id');
//    $query
//      ->fields('tr', [
//        'term_id',
//      ]);
    $query->condition('p.post_type', $this->getPostType());
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id'            => $this->t('Post ID'),
      'post_author'    => $this->t('Post Author'),
      'post_title'    => $this->t('Title'),
      'thumbnail'     => $this->t('Post Thumbnail'),
      'post_excerpt'  => $this->t('Excerpt'),
      'post_content'  => $this->t('Content'),
      'post_date'     => $this->t('Created Date'),
      'post_modified' => $this->t('Modified Date'),
      'path_alias'    => $this->t('URL Alias'),
//      'term_taxonomy_id'    => $this->t('Term Taxonomy ID'),
//      'term_id'    => $this->t('Term ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type'  => 'integer',
        'alias' => 'p',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public function prepareRow(Row $row) {
    // This will generate path alias using WP alias settings.
    $row->setSourceProperty('path_alias', $this->generatePathAlias($row));
    // Get thumbnail ID and pass it to the wp_media migration plugin.
    $row->setSourceProperty('thumbnail', $this->getPostThumbnail($row));
  }

}
