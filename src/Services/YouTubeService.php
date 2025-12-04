<?php

namespace Huement\StatamicYtListr\Services;

use Illuminate\Support\Facades\Http;

class YouTubeService
{
  protected string $apiKey;
  protected string $channelId;
  protected string $baseUrl = 'https://www.googleapis.com/youtube/v3';

  public function __construct(string $apiKey, string $channelId)
  {
    $this->apiKey = $apiKey;
    $this->channelId = $channelId;
  }

  public function fetchLatestVideos(int $maxResults = 50): array
  {
    // First, get the uploads playlist ID
    $channelResponse = Http::get("{$this->baseUrl}/channels", [
      'part' => 'contentDetails',
      'id' => $this->channelId,
      'key' => $this->apiKey,
    ]);

    if (!$channelResponse->successful()) {
      throw new \Exception(
        'Failed to fetch channel data: ' . $channelResponse->body()
      );
    }

    $channelData = $channelResponse->json();

    if (empty($channelData['items'])) {
      throw new \Exception('Channel not found');
    }

    $uploadsPlaylistId =
      $channelData['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

    // Get videos from uploads playlist
    $playlistResponse = Http::get("{$this->baseUrl}/playlistItems", [
      'part' => 'snippet,contentDetails',
      'playlistId' => $uploadsPlaylistId,
      'maxResults' => $maxResults,
      'key' => $this->apiKey,
    ]);

    if (!$playlistResponse->successful()) {
      throw new \Exception(
        'Failed to fetch playlist items: ' . $playlistResponse->body()
      );
    }

    $playlistData = $playlistResponse->json();
    $videoIds = collect($playlistData['items'])
      ->pluck('contentDetails.videoId')
      ->implode(',');

    // Get detailed video information
    $videosResponse = Http::get("{$this->baseUrl}/videos", [
      'part' => 'snippet,contentDetails,statistics',
      'id' => $videoIds,
      'key' => $this->apiKey,
    ]);

    if (!$videosResponse->successful()) {
      throw new \Exception(
        'Failed to fetch video details: ' . $videosResponse->body()
      );
    }

    $videosData = $videosResponse->json();

    return collect($videosData['items'])
      ->map(function ($item) {
        return [
          'video_id' => $item['id'],
          'title' => $item['snippet']['title'],
          'description' => $item['snippet']['description'],
          'thumbnail_url' =>
            $item['snippet']['thumbnails']['high']['url'] ??
            $item['snippet']['thumbnails']['default']['url'],
          'published_at' => $item['snippet']['publishedAt'],
          'duration' => $this->parseDuration(
            $item['contentDetails']['duration']
          ),
          'view_count' => $item['statistics']['viewCount'] ?? 0,
          'like_count' => $item['statistics']['likeCount'] ?? 0,
          'comment_count' => $item['statistics']['commentCount'] ?? 0,
          'channel_id' => $item['snippet']['channelId'],
          'channel_title' => $item['snippet']['channelTitle'],
        ];
      })
      ->toArray();
  }

  protected function parseDuration(string $duration): int
  {
    // Parse ISO 8601 duration (e.g., PT4M13S) to seconds
    preg_match('/PT(\d+H)?(\d+M)?(\d+S)?/', $duration, $matches);

    $hours = isset($matches[1]) ? (int) rtrim($matches[1], 'H') : 0;
    $minutes = isset($matches[2]) ? (int) rtrim($matches[2], 'M') : 0;
    $seconds = isset($matches[3]) ? (int) rtrim($matches[3], 'S') : 0;

    return $hours * 3600 + $minutes * 60 + $seconds;
  }
}
