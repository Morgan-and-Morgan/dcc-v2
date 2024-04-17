<?php

namespace Drupal\dcc_general\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Configure DCC General settings for this site.
 */
class DisabilityEvaluationDefaultPagesBannerSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'disability_evaluation_default_pages__banner_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dcc_general.disability_evaluation_default_pages__banner_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['banner_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_title'),
    ];
    $form['banner_description'] = [
      '#type' => 'textarea',
      '#format' => 'full_html',
      '#title' => $this->t('Description'),
      '#default_value' => $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_description'),
    ];
    $form['banner_button_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button Title'),
      '#default_value' => $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_button_title'),
    ];
    $form['banner_button_link'] = [
      '#type' => 'url',
      '#title' => $this->t('Button link'),
      '#default_value' => $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_button_link'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // @todo add validation checking if needed.
    // Example:
    //    if ($form_state->getValue('banner_title') != 'banner_title') {
    //      $form_state->setErrorByName('banner_title', $this->t('The value is not correct.'));
    //    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')
      ->set('banner_title', $form_state->getValue('banner_title'))
      ->save();
    $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')
      ->set('banner_description', $form_state->getValue('banner_description'))
      ->save();
    $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')
      ->set('banner_button_title', $form_state->getValue('banner_button_title'))
      ->save();
    $this->config('dcc_general.disability_evaluation_default_pages__banner_settings')
      ->set('banner_button_link', $form_state->getValue('banner_button_link'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
