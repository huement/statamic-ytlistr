<?php

namespace Huement\StatamicYtListr\Repositories;

use Illuminate\Support\Carbon;
use Statamic\Facades\YAML;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class YamlVideoRepository implements VideoRepository
{
    /**
     * Get the configured storage directory path context.
     */
    protected function getStoragePath(): string
    {
        return config('statamic-ytlistr.storage_path', 'content/ytlistr');
    }

    /**
     * Resolve the application's local filesystem storage manager instance.
     */
    protected function disk()
    {
        return Storage::disk('local');
    }

    public function all()
    {
        $dir = $this->getStoragePath();
        if (! $this->disk()->exists($dir)) {
            return collect();
        }

        // Pull files cleanly using the test-isolated storage framework adapter
        $files = $this->disk()->files($dir);

        return collect($files)
            ->filter(fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'yaml')
            ->map(fn($file) => collect(YAML::parse($this->disk()->get($file))))
            ->sortByDesc('published_at');
    }

    public function paginate($perPage = 20)
    {
        $items = $this->all();
        $page = LengthAwarePaginator::resolveCurrentPage();
        
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function recentPaginated(int $perPage = 20)
    {
        return $this->paginate($perPage);
    }

    public function recent(?int $limit = null)
    {
        $items = $this->all();

        if ($limit) {
            $items = $items->take($limit);
        }

        return $items;
    }

    public function latest()
    {
        return $this->all()->first();
    }

    public function updateOrCreate(string $videoId, array $data)
    {
        $dir = $this->getStoragePath();
        
        if (! $this->disk()->exists($dir)) {
            $this->disk()->makeDirectory($dir);
        }
        
        $path = "{$dir}/{$videoId}.yaml";
        $this->disk()->put($path, YAML::dump($data));
        
        return collect($data);
    }

    public function delete(string $videoId)
    {
        $path = "{$this->getStoragePath()}/{$videoId}.yaml";
        
        if ($this->disk()->exists($path)) {
            return $this->disk()->delete($path);
        }
        
        return false;
    }

    public function count(): int 
    { 
        return $this->all()->count(); 
    }

    public function sumViews(): int 
    { 
        return $this->all()->sum('view_count'); 
    }

    public function findLatestUpdatedAt(): ?Carbon
    {
        $dir = $this->getStoragePath();
        if (! $this->disk()->exists($dir)) {
            return null;
        }

        $files = $this->disk()->files($dir);
        $yamlFiles = array_filter($files, fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'yaml');

        if (empty($yamlFiles)) {
            return null;
        }

        // Pull the maximum file modification time via the unified storage layer
        $latestTimestamp = collect($yamlFiles)
            ->map(fn($file) => $this->disk()->lastModified($file))
            ->max();

        return $latestTimestamp ? Carbon::createFromTimestamp($latestTimestamp) : null;
    }
}