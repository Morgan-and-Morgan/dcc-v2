<?php

namespace Drupal\dcc_migrations\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Clean up urls before importing.
 *
 * @MigrateProcessPlugin(
 *   id = "embed_videos"
 * )
 */
class EmbedVideos extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $youtube_url = $this->stringBetweenTwoString($value, '[embed]', '[/embed]');

    if ($youtube_url != '') {

      $is_youtube = preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $youtube_url, $matches);

      // Bail out rather than read $matches[1] unconditionally: an [embed] that
      // wraps a non-YouTube URL leaves $matches empty, which PHP 8 reports as
      // an undefined array key and previously produced an iframe pointing at
      // an empty video id.
      if (!$is_youtube) {
        return $value;
      }

      $video_id = $matches[1];

      $pattern = '/\[embed\][\s\S]+\[\/embed\]/';
      $replacement = '<iframe width="640" height="360" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen> </iframe>';
      $string = $value;

      $body = preg_replace($pattern, $replacement, $string);

      return $body;

    }

    return $value;
  }

  /**
   * Returns the substring delimited by two marker strings.
   *
   * @param string $str
   *   The string to search.
   * @param string $starting_word
   *   The opening delimiter.
   * @param string $ending_word
   *   The closing delimiter.
   *
   * @return string
   *   The text between the delimiters, or an empty string when the opening
   *   delimiter is absent.
   */
  public function stringBetweenTwoString($str, $starting_word, $ending_word) {
    $arr = explode($starting_word, $str);
    if (isset($arr[1])) {
      $arr = explode($ending_word, $arr[1]);
      return $arr[0];
    }
    return '';
  }

}
