<?php

namespace Drupal\dcc_general\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a refer clients block form block.
 *
 * @Block(
 *   id = "disability_claim_help_banner_block",
 *   admin_label = @Translation("Disability Claim Help - Banner"),
 *   category = @Translation("Custom")
 * )
 */
class DisabilityClaimHelpBannerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Name of the config object holding this banner's settings.
   */
  const SETTINGS = 'dcc_general.disability_claim_help__banner_settings';

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a DisabilityClaimHelpBannerBlock.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get(self::SETTINGS);

    return [
      '#content' => [
        'banner_title' => $config->get('banner_title') ?: '',
        'banner_description' => $config->get('banner_description') ?: '',
        'banner_button_title' => $config->get('banner_button_title') ?: '',
        'banner_button_link' => $config->get('banner_button_link') ?: '',
      ],
      '#theme' => 'disability_claim_help__banner_block',
      // Without this tag the rendered block is cached with no dependency on
      // the settings it is built from, so saving the banner settings form
      // leaves the old text on the page until caches are cleared by hand.
      '#cache' => [
        'tags' => $config->getCacheTags(),
      ],
    ];
  }

}
