<?php

namespace Drupal\dcc_general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a refer clients block form block.
 *
 * @Block(
 *   id = "disability_claim_help_banner_block",
 *   admin_label = @Translation("Disability Claim Help - Banner"),
 *   category = @Translation("Custom")
 * )
 */
class DisabilityClaimHelpBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#content' => [
        'banner_title' => \Drupal::config('dcc_general.disability_claim_help__banner_settings')->get('banner_title') ?: '',
        'banner_description' => \Drupal::config('dcc_general.disability_claim_help__banner_settings')->get('banner_description') ?: '',
        'banner_button_title' => \Drupal::config('dcc_general.disability_claim_help__banner_settings')->get('banner_button_title') ?: '',
        'banner_button_link' => \Drupal::config('dcc_general.disability_claim_help__banner_settings')->get('banner_button_link') ?: '',
      ],
      '#theme' => 'disability_claim_help__banner_block',
    ];
  }
}
