<?php

namespace Drupal\dcc_general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a refer clients block form block.
 *
 * @Block(
 *   id = "dcc_general_banner_block",
 *   admin_label = @Translation("DCC Banner Block"),
 *   category = @Translation("Custom")
 * )
 */
class BannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#content' => [
        'banner_title' => \Drupal::config('dcc_general.banner_settings')->get('banner_title') ?? '',
        'banner_description' => \Drupal::config('dcc_general.banner_settings')->get('banner_description') ?? '',
        'banner_button_title' => \Drupal::config('dcc_general.banner_settings')->get('banner_button_title') ?? '',
        'banner_button_link' => \Drupal::config('dcc_general.banner_settings')->get('banner_button_link') ?? '',
      ],
      '#theme' => 'dcc_general_banner_block',
    ];
  }
}
