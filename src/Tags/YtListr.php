<?php

namespace Huement\StatamicYtListr\Tags;

use Huement\StatamicYtListr\Repositories\VideoRepository;
use Statamic\Tags\Tags;

class YtListr extends Tags
{
    protected static $handle = 'yt_listr';

    /**
     * Resolve the active repository driver on-demand.
     */
    protected function repo(): VideoRepository
    {
        return app(VideoRepository::class);
    }

    /**
     * {{ yt_listr limit="5" }}
     */
    public function index()
    {
        $limit = $this->params->get('limit') ? (int) $this->params->get('limit') : null;
        
        // Swapped to use the repo() helper method safely
        $videos = $this->repo()->recent($limit);

        return collect($videos)
            ->map(function ($video) {
                $videoId = data_get($video, 'video_id');

                return [
                    'id'                 => data_get($video, 'id'),
                    'video_id'           => $videoId,
                    'title'              => data_get($video, 'title'),
                    'description'        => data_get($video, 'description'),
                    'thumbnail_url'      => data_get($video, 'thumbnail_url'),
                    'published_at'       => data_get($video, 'published_at'),
                    'duration'           => data_get($video, 'duration'),
                    'duration_formatted' => $this->formatDuration((int) data_get($video, 'duration', 0)),
                    'view_count'         => data_get($video, 'view_count', 0),
                    'like_count'         => data_get($video, 'like_count', 0),
                    'comment_count'      => data_get($video, 'comment_count', 0),
                    'channel_id'         => data_get($video, 'channel_id'),
                    'channel_title'      => data_get($video, 'channel_title'),
                    'youtube_url'        => data_get($video, 'youtube_url') ?? "https://youtu.be/{$videoId}",
                    'embed_url'          => data_get($video, 'embed_url') ?? "https://www.youtube.com/embed/{$videoId}",
                ];
            })
            ->toArray();
    }

    /**
     * {{ yt_listr:latest }}
     */
    public function latest()
    {
        $video = $this->repo()->latest();

        if (! $video) {
            return [];
        }

        $videoId = data_get($video, 'video_id');

        return [
            'id'                 => data_get($video, 'id'),
            'video_id'           => $videoId,
            'title'              => data_get($video, 'title'),
            'description'        => data_get($video, 'description'),
            'thumbnail_url'      => data_get($video, 'thumbnail_url'),
            'published_at'       => data_get($video, 'published_at'),
            'duration'           => data_get($video, 'duration'),
            'duration_formatted' => $this->formatDuration((int) data_get($video, 'duration', 0)),
            'view_count'         => data_get($video, 'view_count', 0),
            'like_count'         => data_get($video, 'like_count', 0),
            'comment_count'      => data_get($video, 'comment_count', 0),
            'channel_id'         => data_get($video, 'channel_id'),
            'channel_title'      => data_get($video, 'channel_title'),
            'youtube_url'        => data_get($video, 'youtube_url') ?? "https://youtu.be/{$videoId}",
            'embed_url'          => data_get($video, 'embed_url') ?? "https://www.youtube.com/embed/{$videoId}",
        ];
    }

    /**
     * {{ yt_listr:count }}
     */
    public function count()
    {
        return $this->repo()->count();
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