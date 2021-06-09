<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Return Page alias.
 *
 * @MigrateProcessPlugin(
 *   id = "page_alias"
 * )
 * @codingStandardsIgnoreStart
 *
 *
 * @codingStandardsIgnoreEnd
 */
class PageAlias extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (isset($value)) {
      $original_post = $this->getPostById($value);
      if ($original_post['post_parent'] == '0') {
        $alias = '/' . $original_post['post_name'];
      }
      else {
        $parent = $this->getPostById($original_post['post_parent']);

        if ($parent['post_parent'] == '0') {
          $alias = '/' . $parent['post_name'] . '/' . $original_post['post_name'];
        }
        else {
          $grandparent = $this->getPostById($parent['post_parent']);
          $alias = '/' . $grandparent['post_name'] . '/' . $parent['post_name'] . '/' . $original_post['post_name'];
        }
      }
      return $alias;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get Post by id.
   */
  public function getPostById($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_posts', 'p', ['target' => 'migrate']);
      $query->condition('p.ID', $id);
      $query->condition('p.post_type', 'page');
      $query->addField('p', 'post_parent', 'post_parent');
      $query->addField('p', 'post_name', 'post_name');
      $result = $query->execute()->fetchAll();
      $post_data = [];
      foreach ($result as $item) {
        $post_data['post_name'] = $item->post_name;
        $post_data['post_parent'] = $item->post_parent;
      }
      return $post_data;
    }
    else {
      return NULL;
    }
  }

}
