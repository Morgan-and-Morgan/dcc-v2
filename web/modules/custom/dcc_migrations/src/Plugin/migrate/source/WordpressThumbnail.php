<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\dcc_migrations\Plugin\migrate\source\MySqlBase;
use Drupal\migrate\Row;

/**
 * Extract content thumbnails.
 *
 * @MigrateSource(
 *   id = "wp_thumbnail"
 * )
 */
class WordpressThumbnail extends MySqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $prefix = $this->getPrefix();
    $query = $this->select($prefix . '_postmeta', 'pm', ['target' => 'migrate']);
    $query->innerJoin($prefix . '_postmeta', 'pm2', 'pm2.post_id = pm.meta_value');
    $query->innerJoin($prefix . '_posts', 'p', 'p.id = pm.post_id');
    $query->fields('pm', ['post_id']);
    $query->fields('p', ['post_date']);
    $query->addField('pm2', 'post_id', 'file_id');
    $query->addField('pm2', 'meta_value', 'filepath');
    $query
      ->condition('pm.meta_key', '_thumbnail_id')
      ->condition('pm2.meta_key', '_wp_attached_file')
      ->condition('p.post_status', 'publish');
    //      ->condition('p.post_type', 'post');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'post_id'   => $this->t('Post ID'),
      'post_date' => $this->t('Media Uploaded Date'),
      'file_id'   => $this->t('File ID'),
      'filepath'  => $this->t('File Path'),
      'filename'  => $this->t('File Name'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'post_id' => [
        'type'  => 'integer',
        'alias' => 'pm2',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public function prepareRow(Row $row) {
    //    drush_print($row); die();
    $row->setSourceProperty('filename', basename($row->getSourceProperty('filepath')));
  }

}
