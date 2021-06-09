<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

// Use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 *
 */
class MySqlBase extends SqlBase {

  /**
   * Get database table prefix from the migration template.
   */
  protected function getPrefix() {
    return !empty($this->configuration['table_prefix']) ? $this->configuration['table_prefix'] : 'wp_33';
  }

  /**
   * Get Wordpress post type from the migration template.
   */
  protected function getPostType() {
    return !empty($this->configuration['post_type']) ? $this->configuration['post_type'] : 'post';
  }

  /**
   * Generate path alias via pattern specified in `permalink_structure`.
   */
  protected function generatePathAlias(Row $row) {
    $prefix = $this->getPrefix();
    $permalink_structure = $this->select($prefix . '_options', 'o', ['target' => 'migrate'])
      ->fields('o', ['option_value'])
      ->condition('o.option_name', 'permalink_structure')
      ->execute()
      ->fetchField();
    $date = new \DateTime($row->getSourceProperty('post_date'));
    $parameters = [
      '%year%'     => $date->format('Y'),
      '%monthnum%' => $date->format('m'),
      '%day%'      => $date->format('d'),
      '%postname%' => $row->getSourceProperty('post_name'),
    ];
    $url = str_replace(array_keys($parameters), array_values($parameters), $permalink_structure);
    return rtrim($url, '/');
  }

  /**
   * Get post thumbnail.
   */
  protected function getPostThumbnail(Row $row) {
    $prefix = $this->getPrefix();
    $query = $this->select($prefix . '_postmeta', 'pm', ['target' => 'migrate']);
    $query->innerJoin($prefix . '_postmeta', 'pm2', 'pm2.post_id = pm.meta_value');
    $query->fields('pm', ['post_id'])
      ->condition('pm.post_id', $row->getSourceProperty('id'))
      ->condition('pm.meta_key', '_thumbnail_id')
      ->condition('pm2.meta_key', '_wp_attached_file');
    return $query->execute()->fetchField();
  }

  /**
   * @inheritDoc
   */
  public function query() {
    // @todo Implement query() method.
  }

  /**
   * @inheritDoc
   */
  public function getIds() {
    // @todo Implement getIds() method.
  }

  /**
   * @inheritDoc
   */
  public function fields() {
    // @todo Implement fields() method.
  }

}
