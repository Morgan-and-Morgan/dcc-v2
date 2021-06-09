<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for cases.
 *
 * @MigrateSource(
 *   id = "wordpress_users"
 * )
 */
class WordpressUsers extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('wp_users', 'u')
      ->fields('u', array(
        'ID',
        'user_login',
        'user_pass',
        'user_email',
        'user_registered',
      ));
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = array(
      'ID' => $this->t('ID'),
      'user_login' => $this->t('User Login'),
      'user_pass' => $this->t('User Password'),
      'user_email' => $this->t('User Email'),
      'user_registered' => $this->t('User Registered'),
    );
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'ID' => [
        'type' => 'integer',
      ],
    ];
  }
}
