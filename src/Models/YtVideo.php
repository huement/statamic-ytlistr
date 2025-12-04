<?php

namespace Huement\StatamicYtListr\Models;

use Illuminate\Database\Eloquent\Model;

class YtVideo extends Model
{
  protected $table = 'yt_listr_videos';

  protected $fillable = [
    'video_id',
    'title',
    'description',
    'thumbnail_url',
    'published_at',
    'duration',
    'view_count',
    'like_count',
    'comment_count',
    'channel_id',
    'channel_title',
  ];

  protected $casts = [
    'published_at' => 'datetime',
    'view_count' => 'integer',
    'like_count' => 'integer',
    'comment_count' => 'integer',
  ];

  public function getYoutubeUrlAttribute(): string
  {
    return "https://www.youtube.com/watch?v={$this->video_id}";
  }

  public function getEmbedUrlAttribute(): string
  {
    return "https://www.youtube.com/embed/{$this->video_id}";
  }

  public function scopeRecent($query)
  {
    return $query->orderBy('published_at', 'desc');
  }
}
