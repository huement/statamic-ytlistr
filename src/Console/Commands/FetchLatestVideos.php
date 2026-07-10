<?php

namespace Huement\StatamicYtListr\Console\Commands;

use Huement\StatamicYtListr\Repositories\VideoRepository;
use Huement\StatamicYtListr\Services\YouTubeService;
use Illuminate\Console\Command;

class FetchLatestVideos extends Command
{
  protected $signature = 'ytlistr:fetch {--limit=50 : Maximum number of videos to fetch}';

  protected $description = 'Fetch the latest videos from YouTube and sync to database';

  // Declaring the property ensures $this->repository is defined
  protected VideoRepository $repository;

  public function handle(): int
  {
    $apiKey = config('statamic-ytlistr.api_key');
    $channelId = config('statamic-ytlistr.channel_id');

    if (empty($apiKey) || empty($channelId)) {
      $this->error('YouTube API key or Channel ID not configured.');
      $this->info(
        'Please set YOUTUBE_API_KEY and YOUTUBE_CHANNEL_ID in your .env file.'
      );
      return self::FAILURE;
    }

    $this->info('Fetching videos from YouTube...');
    $this->repository = app(VideoRepository::class);

    try {
      $service = new YouTubeService($apiKey, $channelId);
      $videos = $service->fetchLatestVideos((int) $this->option('limit'));

      $synced = 0;
      $updated = 0;

      foreach ($videos as $videoData) {
        $video = $this->repository->updateOrCreate(
          $videoData['video_id'],
          $videoData
        );

        if ($video->wasRecentlyCreated) {
          $synced++;
        } else {
          $updated++;
        }
      }

      $this->info(
        "✓ Successfully synced {$synced} new videos and updated {$updated} existing videos."
      );

      return self::SUCCESS;
    } catch (\Exception $e) {
      $this->error('Failed to fetch videos: ' . $e->getMessage());
      return self::FAILURE;
    }
  }
}
