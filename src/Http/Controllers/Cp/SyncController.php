<?php

namespace Huement\StatamicYtListr\Http\Controllers\Cp;

use Huement\StatamicYtListr\Repositories\VideoRepository;
use Huement\StatamicYtListr\Services\YouTubeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Statamic\Http\Controllers\CP\CpController;

class SyncController extends CpController
{
  protected VideoRepository $repository;

  public function __construct(VideoRepository $repository)
  {
    $this->repository = $repository;
  }

  public function index(): View
  {
      $videos = $this->repository->recentPaginated(20);
      $config = config('statamic-ytlistr');
      $isConfigured = !empty($config['api_key']) && !empty($config['channel_id']);

      // FIXED: The repository now returns a clean Carbon instance or null directly!
      $lastSync = $this->repository->findLatestUpdatedAt();

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
        $this->repository->updateOrCreate(
          $videoData['video_id'],
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

  public function destroy(string $videoId): RedirectResponse
  {
    $this->repository->delete($videoId);

    return redirect()
      ->route('statamic.cp.statamic-ytlistr.index')
      ->with('success', 'Video deleted successfully.');
  }
}
