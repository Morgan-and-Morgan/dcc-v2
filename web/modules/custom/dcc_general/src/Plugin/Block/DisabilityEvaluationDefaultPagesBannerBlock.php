<?php

namespace Drupal\dcc_general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a refer clients block form block.
 *
 * @Block(
 *   id = "disability_evaluation_default_pages_banner_block",
 *   admin_label = @Translation("Disability Evaluation - Default Pages - Banner"),
 *   category = @Translation("Custom")
 * )
 */
class DisabilityEvaluationDefaultPagesBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#content' => [
        'banner_title' => \Drupal::config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_title') ?: '',
        'banner_description' => \Drupal::config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_description') ?: '',
        'banner_button_title' => \Drupal::config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_button_title') ?: '',
        'banner_button_link' => \Drupal::config('dcc_general.disability_evaluation_default_pages__banner_settings')->get('banner_button_link') ?: '',
      ],
      '#theme' => 'disability_evaluation_default_pages__banner_block',
    ];
  }
}
