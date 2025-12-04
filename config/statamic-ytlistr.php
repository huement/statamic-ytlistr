<?php

return [
    /*
    |--------------------------------------------------------------------------
    | YouTube API Key
    |--------------------------------------------------------------------------
    |
    | Your YouTube Data API v3 key. Get one from:
    | https://console.cloud.google.com/apis/credentials
    |
    */
    'api_key' => env('YOUTUBE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | YouTube Channel ID
    |--------------------------------------------------------------------------
    |
    | The YouTube channel ID to sync videos from.
    | You can find this in your YouTube channel URL or settings.
    |
    */
    'channel_id' => env('YOUTUBE_CHANNEL_ID'),

    /*
    |--------------------------------------------------------------------------
    | Maximum Results
    |--------------------------------------------------------------------------
    |
    | The maximum number of videos to fetch per sync operation.
    |
    */
    'max_results' => env('YOUTUBE_MAX_RESULTS', 50),
];

