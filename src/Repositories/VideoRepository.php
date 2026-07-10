<?php

namespace Huement\StatamicYtListr\Repositories;

interface VideoRepository
{
  public function all();
  public function paginate($perPage = 20);
  public function updateOrCreate(string $videoId, array $data);
  public function delete(string $videoId);
  public function count(): int;
  public function sumViews(): int;
  public function latest();
  public function recent(?int $limit = null);
  public function recentPaginated(int $perPage = 20);
  /**
   * Get the latest update timestamp across all tracking records.
   */
  public function findLatestUpdatedAt(): ?\Illuminate\Support\Carbon;
}
