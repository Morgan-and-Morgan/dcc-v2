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
    // Taxonomy terms are deliberately not joined in here. Joining
    // wp_33_term_relationships and wp_33_terms to select term_taxonomy_id and
    // term_id multiplies the result set by the number of terms per post, which
    // makes the source yield one row per post/term pair rather than one per
    // post. Term assignment is handled by a separate migration instead.
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
      // No term_taxonomy_id or term_id field: see the note in query() on why
      // taxonomy is not joined into this source.
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
