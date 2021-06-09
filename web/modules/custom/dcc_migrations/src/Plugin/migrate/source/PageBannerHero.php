<?php

namespace Drupal\dcc_migrations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * Source plugin for class action articles.
 *
 * @MigrateSource(
 *   id = "page_banner_hero"
 * )
 */
class PageBannerHero extends SqlBase {

  /**
   * Get All data for page banner.
   */

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('wp_33_posts', 'wpp');
    $query->condition('wpp.post_type', 'page', '=');
    $query->condition('wpp.post_status', 'publish', '=');
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
        'alias' => 'wpp',
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

    // Custom Title.
    $custom_title = $this->getCustomTitle($post_id);
    if (isset($custom_title) && $custom_title != NULL) {
      $row->setSourceProperty('custom_title', $custom_title);
    }
    else {
      $row->setSourceProperty('custom_title', $this->getPostTitle($post_id));
    }
    // Custom Description.
    $row->setSourceProperty('custom_description', $this->getCustomDesc($post_id));

    // Phone Text Headline.
    $text_headline = $this->getTextHeadline($post_id);
    if (isset($text_headline) && $text_headline != NULL) {
      $row->setSourceProperty('text_action_headline', $text_headline);
    }
    else {
      $row->setSourceProperty('text_action_headline', $this->getAltTextHeadline($post_id));
    }
    // Phone Text.
    $phone_text = $this->getPhoneText($post_id);
    if (isset($phone_text) && $phone_text != NULL) {
      $row->setSourceProperty('phone_text', $phone_text);
    }
    else {
      $row->setSourceProperty('phone_text', $this->getAltPhoneText($post_id));
    }
    // Phone Url.
    $phone_url = $this->getPhoneUrl($post_id);
    if (isset($phone_url) && $phone_url != NULL) {
      $row->setSourceProperty('phone_url', $phone_url);
    }
    else {
      $row->setSourceProperty('phone_url', $this->getAltPhoneUrl($post_id));
    }
    return parent::prepareRow($row);
  }

  /**
   *
   */
  public function getHeroImage($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $post_id);
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

  /**
   * Get custom Title.
   */
  public function getCustomTitle($post_id) {
    if (isset($id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $post_id);
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
  public function getPostTitle($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_posts', 'p', ['target' => 'migrate']);
      $query->condition('p.ID', $post_id);
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
  public function getCustomDesc($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $query->condition('meta.post_id', $post_id);
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
  public function getTextHeadline($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $andGroup = $query->andConditionGroup()
        ->condition('meta.post_id', $post_id)
        ->condition('meta.meta_key', 'text_action_headline');
      $query->condition($andGroup);
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
   * Get Alt Text Headline.
   */
  public function getAltTextHeadline($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $andGroup = $query->andConditionGroup()
        ->condition('meta.post_id', $post_id)
        ->condition('meta.meta_key', 'cta_0_cta_actions_0_text_action_headline');
      $query->condition($andGroup);
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
  public function getPhoneText($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $andGroup = $query->andConditionGroup()
        ->condition('meta.post_id', $post_id)
        ->condition('meta.meta_key', 'phone_text');
      $query->condition($andGroup);
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
   * Get Alt Phone Text.
   */
  public function getAltPhoneText($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $andGroup = $query->andConditionGroup()
        ->condition('meta.post_id', $post_id)
        ->condition('meta.meta_key', 'cta_0_cta_actions_0_text_action_text');
      $query->condition($andGroup);
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
  public function getPhoneUrl($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $andGroup = $query->andConditionGroup()
        ->condition('meta.post_id', $post_id)
        ->condition('meta.meta_key', 'phone_url');
      $query->condition($andGroup);
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

  /**
   * Get Alt Phone Url.
   */
  public function getAltPhoneUrl($post_id) {
    if (isset($post_id)) {
      $con = Database::getConnection('default', 'migrate');
      $query = $con->select('wp_33_postmeta', 'meta', ['target' => 'migrate']);
      $andGroup = $query->andConditionGroup()
        ->condition('meta.post_id', $post_id)
        ->condition('meta.meta_key', 'cta_0_cta_actions_0_text_action_url');
      $query->condition($andGroup);
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
