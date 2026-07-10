<?php

namespace Huement\StatamicYtListr\Repositories;

use Huement\StatamicYtListr\Models\YtVideo;
use Illuminate\Support\Carbon;

class DatabaseVideoRepository implements VideoRepository
{
  public function all()
  {
    return YtVideo::all();
  }
  public function paginate($perPage = 20)
  {
    return YtVideo::paginate($perPage);
  }

  public function updateOrCreate(string $videoId, array $data)
  {
    return YtVideo::updateOrCreate(['video_id' => $videoId], $data);
  }

  public function delete(string $videoId)
  {
    return YtVideo::where('video_id', $videoId)->delete();
  }

  public function count(): int
  {
    return YtVideo::count();
  }
  public function sumViews(): int
  {
    return YtVideo::sum('view_count');
  }

  public function latest()
  {
    return YtVideo::latest('published_at')->first();
  }

  public function recent(?int $limit = null)
  {
    $query = \Huement\StatamicYtListr\Models\YtVideo::latest('published_at');

    if ($limit) {
        $query->limit($limit);
    }

    return $query->get();
  }

  public function recentPaginated(int $perPage = 20)
  {
      // Adjust this if your model uses a custom query scope, 
      // otherwise sorting by published_at desc is standard
      return YtVideo::latest('published_at')->paginate($perPage);
  }

  public function findLatestUpdatedAt(): ?\Illuminate\Support\Carbon
  {
      $video = YtVideo::latest('updated_at')->first();

      return $video ? $video->updated_at : null;
  }
}
