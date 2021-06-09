<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\Core\Database\Database;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for class action articles.
 *
 * @MigrateSource(
 *   id = "hero_banner_source"
 * )
 */
class HeroBannerSource extends SqlBase {

  /**
   * Get Hero Banner data.
   */

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('wp_33_posts', 'p');
    $query->condition('p.post_type', 'post', '=');
    $query->fields('p', [
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
      'hero_image_id' => $this->t('Hero Image id'),
      'custom_title' => $this->t('Custom Title'),
      'custom_description' => $this->t('Custom Desc'),
      'text_action_headline' => $this->t('Text Action Headline'),
      'phone_text' => $this->t('Phone Text'),
      'phone_url' => $this->t('Phone URL'),
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
      ],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public function prepareRow(Row $row) {
    $post_id = $row->getSourceProperty('ID');
    $row->setSourceProperty('hero_image_id', $this->getHeroImage($post_id));

    $custom_title = $this->getCustomTitle($post_id);
    if (isset($custom_title)) {
      $row->setSourceProperty('custom_title', $custom_title);
    }
    else {
      $row->setSourceProperty('custom_title', $this->getPostTitle($post_id));
    }
    $row->setSourceProperty('custom_description', $this->getCustomDesc($post_id));
    $row->setSourceProperty('text_action_headline', $this->getTextHeadline($post_id));
    $row->setSourceProperty('phone_text', $this->getPhoneText($post_id));
    $row->setSourceProperty('phone_url', $this->getPhoneUrl($post_id));

    return parent::prepareRow($row);
  }

  /**
   *
   */
  public function getHeroImage($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $id);
      $query->condition('meta.meta_key', 'hero_image_uploader');
      $query->addField('meta', 'post_id', 'post_id');
      $query->addField('meta', 'meta_value', 'hero_image_uploader');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $value = $item->hero_image_uploader;
      }
      return $value;
    }
    else {
      return NULL;
    }
  }

  // /**
  //   * Get custom Title.
  //   */
  //  public function getCustomTitle($id) {
  //    if (isset($id)) {
  //      $con = Database::getConnection('default', 'migrate');
  //      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
  //      $query->condition('meta.post_id', $id);
  //      $query->condition('meta.meta_key', 'custom_title');
  //      $query->addField('meta', 'post_id', 'post_id');
  //      $query->addField('meta', 'meta_value', 'custom_title');
  //      $result = $query->execute()->fetchAll();
  //      foreach ($result as $item) {
  //        $value = $item->custom_title;
  //      }
  //      return $value;
  //    }
  //    else {
  //      return NULL;
  //    }
  //  }

  /**
   * Get custom Title.
   */
  public function getCustomTitle($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $id);
      $query->condition('meta.meta_key', 'custom_title');
      $query->addField('meta', 'meta_value', 'custom_title');
      $result = $query->execute()->fetchAll();
      if ($result) {
        foreach ($result as $item) {
          $value = $item->custom_title;
        }
        return $value;
      }
      else {
        return NULL;
      }
    }
  }

  /**
   * Get custom Title.
   */
  public function getPostTitle($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_posts', 'p', ['target' => 'migrate']);
      $query->condition('p.ID', $id);
      $query->addField('p', 'post_title', 'post_title');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $value = $item->post_title;
      }
      return $value;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get custom Desc.
   */
  public function getCustomDesc($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $id);
      $query->condition('meta.meta_key', 'custom_description');
      $query->addField('meta', 'post_id', 'post_id');
      $query->addField('meta', 'meta_value', 'custom_description');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $value = $item->custom_description;
      }
      return $value;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get Text Headline.
   */
  public function getTextHeadline($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $id);
      $query->condition('meta.meta_key', 'text_action_headline');
      $query->addField('meta', 'post_id', 'post_id');
      $query->addField('meta', 'meta_value', 'text_action_headline');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $value = $item->text_action_headline;
      }
      return $value;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get Phone Text.
   */
  public function getPhoneText($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $id);
      $query->condition('meta.meta_key', 'phone_text');
      $query->addField('meta', 'post_id', 'post_id');
      $query->addField('meta', 'meta_value', 'phone_text');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $value = $item->phone_text;
      }
      return $value;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get Phone Url.
   */
  public function getPhoneUrl($id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $id);
      $query->condition('meta.meta_key', 'phone_url');
      $query->addField('meta', 'post_id', 'post_id');
      $query->addField('meta', 'meta_value', 'phone_url');
      $result = $query->execute()->fetchAll();
      foreach ($result as $item) {
        $value = $item->phone_url;
      }
      return $value;
    }
    else {
      return NULL;
    }
  }

}
