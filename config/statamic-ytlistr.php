<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Options
    |--------------------------------------------------------------------------
    |
    | Where / how the fetched YouTube video data should be stored.
    | Supported options: 'database', 'flat_file'
    |
    */
    'driver' => env('YTLISTR_STORAGE_DRIVER', 'database'),

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

    /*
    |--------------------------------------------------------------------------
    | Automated Syncing
    |--------------------------------------------------------------------------
    |
    | Enable or disable the daily background sync.
    |
    */
    'scheduler' => [
        /*
        |--------------------------------------------------------------------------
        | Enable background automation
        |--------------------------------------------------------------------------
        */
        'enabled' => true,

        /*
        |--------------------------------------------------------------------------
        | Sync Frequency
        |--------------------------------------------------------------------------
        | Supported options: 'daily', 'hourly', 'cron'
        |
        */
        'frequency' => 'daily',

        /*
        |--------------------------------------------------------------------------
        | Custom Time (for 'daily' frequency)
        |--------------------------------------------------------------------------
        | Set the time in 24-hour format (HH:MM) when 'daily' is selected.
        |
        */
        'time' => '00:00',

        /*
        |--------------------------------------------------------------------------
        | Custom Cron Expression (for 'cron' frequency)
        |--------------------------------------------------------------------------
        | If frequency is set to 'cron', this expression will be used.
        | Example for 3 times a day (at midnight, 8 AM, and 4 PM): '0 0,8,16 * * *'
        |
        */
        'cron' => '0 0 * * *',
    ],
];
