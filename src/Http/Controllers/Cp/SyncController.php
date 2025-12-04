<?php

namespace Huement\StatamicYtListr\Http\Controllers\Cp;

use Huement\StatamicYtListr\Models\YtVideo;
use Huement\StatamicYtListr\Services\YouTubeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Statamic\Http\Controllers\CP\CpController;

class SyncController extends CpController
{
  public function index(): View
  {
    $videos = YtVideo::recent()->paginate(20);
    $config = config('statamic-ytlistr');
    $isConfigured = !empty($config['api_key']) && !empty($config['channel_id']);

    $lastSyncVideo = YtVideo::latest('updated_at')->first();
    $lastSync = $lastSyncVideo ? $lastSyncVideo->updated_at : null;

    return view('statamic-ytlistr::cp.index', [
      'videos' => $videos,
      'isConfigured' => $isConfigured,
      'lastSync' => $lastSync,
    ]);
  }

  public function sync(): RedirectResponse
  {
    try {
      $service = new YouTubeService(
        config('statamic-ytlistr.api_key'),
        config('statamic-ytlistr.channel_id')
      );

      $videos = $service->fetchLatestVideos(
        config('statamic-ytlistr.max_results', 50)
      );

      $synced = 0;
      foreach ($videos as $videoData) {
        YtVideo::updateOrCreate(
          ['video_id' => $videoData['video_id']],
          $videoData
        );
        $synced++;
      }

      return redirect()
        ->route('statamic.cp.statamic-ytlistr.index')
        ->with('success', "Successfully synced {$synced} videos from YouTube.");
    } catch (\Exception $e) {
      Log::error('YouTube sync failed: ' . $e->getMessage());

      return redirect()
        ->route('statamic.cp.statamic-ytlistr.index')
        ->with('error', 'Sync failed: ' . $e->getMessage());
    }
  }

  public function destroy(YtVideo $video): RedirectResponse
  {
    $video->delete();

    return redirect()
      ->route('statamic.cp.statamic-ytlistr.index')
      ->with('success', 'Video deleted successfully.');
  }
}
