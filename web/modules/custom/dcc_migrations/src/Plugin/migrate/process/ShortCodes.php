<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Processor\Processor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Thunder\Shortcode\Parser\RegexParser;
use Thunder\Shortcode\Syntax\Syntax;
use Thunder\Shortcode\Syntax\SyntaxBuilder;
use Thunder\Shortcode\Parser\RegularParser;

/**
 * Apply the automatic paragraph filter to content.
 *
 * @MigrateProcessPlugin(
 *   id = "short_codes"
 * )
 * @codingStandardsIgnoreStart
 *
 *
 * @codingStandardsIgnoreEnd
 */
class ShortCodes extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $replace_button = $this->buttonCode($value);
    $replace_caption = $this->captionCode($replace_button);

    return $this->readMoreCode($replace_caption);
  }

  /**
   * Decode Readmore shortcode.
   */
  public function readMoreCode($text) {
    $builder = new SyntaxBuilder();

    $doubleSyntax = new Syntax('[', ']', '/', '=', '');
    // Actually using builder.
    $doubleSyntax = $builder
      ->setOpeningTag('[')
      ->setClosingTag(']')
      ->setClosingTagMarker('/')
      ->setParameterValueSeparator('=')
      ->setParameterValueDelimiter('')
      ->getSyntax();

    $handlers = new HandlerContainer();
    $handlers->add('readmore', function (ShortcodeInterface $s) {
      $thumb = $s->getParameter('thumb') ? 'style="background: url( ' . $s->getParameter('thumb') . ') no-repeat center center / cover;"' : '';
      $href = $s->getParameter('href') ? 'href="' . $s->getParameter('href') . '"' : '';
      $content = $s->getContent() ? $s->getContent() : '';
      return '<aside class="read-more-wrapper"><a class="read-more"' . $href . 'target="_blank"><div class="read-more-thumbnail"' . $thumb . '></div><div class="read-more-text"><h3 class="read-more-label">Read More</h3><div class="read-more-title">' . $content . '</div></div></a></aside>';
    });
    $doubleRegex = new Processor(new RegexParser($doubleSyntax), $handlers);

    return $doubleRegex->process($text);
  }

  /**
   * Decode button shortcode.
   */
  public function buttonCode($text) {
    $builder = new SyntaxBuilder();

    $doubleSyntax = new Syntax('[', ']', '/', '=', '""');
    $doubleSyntax = $builder // actually using builder
    ->setOpeningTag('[')
      ->setClosingTag(']')
      ->setClosingTagMarker('/')
      ->setParameterValueSeparator('=')
      ->setParameterValueDelimiter('""')
      ->getSyntax();

    $handlers = new HandlerContainer();
    $handlers->add('button', function(ShortcodeInterface $s) {
      $thumb = $s->getParameter('thumb') ? 'style="background: url( ' . $s->getParameter('thumb') . ') no-repeat center center / cover;"' : '';
      $href = $s->getParameter('href') ? 'href=' . $s->getParameter('href') . '' : '';
      $content = $s->getContent() ? $s->getContent() : '';
      return '<a ' . $href . ' class="button--generic">' . $content . '</a>';
    });
    $doubleRegex = new Processor(new RegexParser($doubleSyntax), $handlers);


    return $doubleRegex->process($text);
  }

  /**
   * Decode button shortcode.
   */
  public function captionCode($text) {
    $captionbuilder = new SyntaxBuilder();

    $captiondoubleSyntax = new Syntax('[', ']', '/', '=', '""');
    $captiondoubleSyntax = $captionbuilder // actually using builder
    ->setOpeningTag('[')
      ->setClosingTag(']')
      ->setClosingTagMarker('/')
      ->setParameterValueSeparator('=')
      ->setParameterValueDelimiter('""')
      ->getSyntax();

    $captionhandlers = new HandlerContainer();
    $captionhandlers->add('caption', function(ShortcodeInterface $s) {
      $id = $s->getParameter('id') ? 'id="' . str_replace('"', '', $s->getParameter('id')) . '"' : "";
      $width = $s->getParameter('width') ? 'style="width: ' . str_replace('"', '', $s->getParameter('width')) . 'px"' : "";
      $align = $s->getParameter('align') ? 'class="wp_caption ' . str_replace('"', '', $s->getParameter('align')) . '"' : '';
      $content = $s->getContent();
      $exploded_content = explode("/>",$content);
      $imgdiv = $exploded_content['0'] ? $exploded_content['0'] . "/>" : "";
      $textafterimage = $exploded_content['1'] ? ltrim($exploded_content['1']) : "Caption";

      return '<figure ' . $id . ' ' . $width . ' ' . $align . '>' . $imgdiv . '<figcaption class="wp-caption-text">' . $textafterimage . '</figcaption></figure>';
    });
    $captiondoubleRegex = new Processor(new RegexParser($captiondoubleSyntax), $captionhandlers);
    return $captiondoubleRegex->process($text);
  }

//
//  /**
//   * Decode download shortcode.
//   */
//  public function downloadCode($text) {
//
//  }
//

}
