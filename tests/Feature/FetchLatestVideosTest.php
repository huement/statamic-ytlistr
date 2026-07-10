<?php

namespace Tests\Feature;

use Huement\StatamicYtListr\Repositories\VideoRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Mockery;

it('creates new records in the database when API returns videos', function () {
    config([
        'statamic-ytlistr.api_key' => 'fake_api_key_matrix',
        'statamic-ytlistr.channel_id' => 'fake_channel_id_vector',
    ]);

    // 1. Setup the Mockery Repository spy to expect exactly one updateOrCreate call
    $mockRepo = Mockery::mock(VideoRepository::class);
    $mockRepo->shouldReceive('updateOrCreate')
        ->once()
        ->with('dQw4w9WgXcQ', Mockery::type('array'))
        ->andReturn(collect([]));
    
    // Bind the mock instance into the application service container
    $this->app->instance(VideoRepository::class, $mockRepo);

    // 2. Setup the HTTP mock environment for the 3-step YouTube API loop
    Http::fake([
        '*googleapis.com/youtube/v3/channels*' => Http::response([
            'items' => [
                [
                    'id' => 'fake_channel_id_vector',
                    'contentDetails' => [
                        'relatedPlaylists' => [
                            'uploads' => 'UU_fake_playlist_id'
                        ]
                    ]
                ]
            ]
        ], 200),

        '*googleapis.com/youtube/v3/playlistItems*' => Http::response([
            'items' => [
                [
                    'snippet' => [
                        'channelId' => 'fake_channel_id_vector',
                        'channelTitle' => 'Huement',
                        'resourceId' => [
                            'videoId' => 'dQw4w9WgXcQ'
                        ]
                    ]
                ]
            ]
        ], 200),

        '*' => Http::response([
            'items' => [
                [
                    'id' => 'dQw4w9WgXcQ',
                    'snippet' => [
                        'channelId' => 'fake_channel_id_vector',
                        'channelTitle' => 'Huement',
                        'title' => 'Never Gonna Give You Up',
                        'description' => 'Rick Astley masterpiece.',
                        'publishedAt' => '2006-10-25T00:00:00Z',
                        'thumbnails' => [
                            'high' => ['url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg']
                        ]
                    ],
                    'statistics' => [
                        'viewCount' => 123456,
                    ],
                    'contentDetails' => [
                        'duration' => 'PT3M33S'
                    ]
                ]
            ]
        ], 200)
    ]);

    // 3. Fire the command execution cycle
    Artisan::call('ytlistr:fetch');
});