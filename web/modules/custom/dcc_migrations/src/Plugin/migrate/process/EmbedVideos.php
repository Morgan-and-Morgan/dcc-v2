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
   *
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {


    $youtube_url = $this->string_between_two_string($value,'[embed]','[/embed]');

    if ($youtube_url != '') {

      preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $youtube_url, $matches);

      $video_id = $matches[1];

      $pattern = '/\[embed\][\s\S]+\[\/embed\]/';
      $replacement = '<iframe width="640" height="360" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen> </iframe>';
      $string = $value;

      $body = preg_replace($pattern,$replacement,$string);

      return $body;

    }

    return $value;
  }

  /**
   * @param $str
   * @param $starting_word
   * @param $ending_word
   * @return mixed|string
   */
  public function string_between_two_string($str, $starting_word, $ending_word){
    $arr = explode($starting_word, $str);
    if (isset($arr[1])){
      $arr = explode($ending_word, $arr[1]);
      return $arr[0];
    }
    return '';
  }

}
