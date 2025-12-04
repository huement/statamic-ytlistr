<?php

namespace Huement\StatamicYtListr\Tags;

use Huement\StatamicYtListr\Models\YtVideo;
use Statamic\Tags\Tags;

class YtListr extends Tags
{
  protected static $handle = 'yt_listr';

  /**
   * {{ yt_listr }}
   *
   * Returns all videos or filtered results
   */
  public function index()
  {
    $limit = $this->params->get('limit');
    $query = YtVideo::recent();

    if ($limit) {
      $query->limit($limit);
    }

    return $query
      ->get()
      ->map(function ($video) {
        return [
          'id' => $video->id,
          'video_id' => $video->video_id,
          'title' => $video->title,
          'description' => $video->description,
          'thumbnail_url' => $video->thumbnail_url,
          'published_at' => $video->published_at,
          'duration' => $video->duration,
          'duration_formatted' => $this->formatDuration($video->duration),
          'view_count' => $video->view_count,
          'like_count' => $video->like_count,
          'comment_count' => $video->comment_count,
          'channel_id' => $video->channel_id,
          'channel_title' => $video->channel_title,
          'youtube_url' => $video->youtube_url,
          'embed_url' => $video->embed_url,
        ];
      })
      ->toArray();
  }

  /**
   * {{ yt_listr:latest }}
   *
   * Returns a single latest video
   */
  public function latest()
  {
    $video = YtVideo::recent()->first();

    if (!$video) {
      return [];
    }

    return [
      'id' => $video->id,
      'video_id' => $video->video_id,
      'title' => $video->title,
      'description' => $video->description,
      'thumbnail_url' => $video->thumbnail_url,
      'published_at' => $video->published_at,
      'duration' => $video->duration,
      'duration_formatted' => $this->formatDuration($video->duration),
      'view_count' => $video->view_count,
      'like_count' => $video->like_count,
      'comment_count' => $video->comment_count,
      'channel_id' => $video->channel_id,
      'channel_title' => $video->channel_title,
      'youtube_url' => $video->youtube_url,
      'embed_url' => $video->embed_url,
    ];
  }

  /**
   * {{ yt_listr:count }}
   *
   * Returns the total count of videos
   */
  public function count()
  {
    return YtVideo::count();
  }

  protected function formatDuration(int $seconds): string
  {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    if ($hours > 0) {
      return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
    }

    return sprintf('%d:%02d', $minutes, $secs);
  }
}
