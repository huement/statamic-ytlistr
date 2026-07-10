<?php

namespace Tests\Feature;

use Huement\StatamicYtListr\Repositories\VideoRepository;
use Huement\StatamicYtListr\Repositories\DatabaseVideoRepository;
use Huement\StatamicYtListr\Repositories\YamlVideoRepository;
use Statamic\Facades\File;

// Clean up our temporary test folders before and after the suite runs
beforeEach(function () {
    config(['statamic-ytlistr.storage_path' => 'content/ytlistr_test']);
    if (File::exists('content/ytlistr_test')) {
        File::deleteDir('content/ytlistr_test');
    }
});

afterEach(function () {
    if (File::exists('content/ytlistr_test')) {
        File::deleteDir('content/ytlistr_test');
    }
});

it('resolves the database repository by default', function () {
    config(['statamic-ytlistr.driver' => 'database']);
    
    $repo = app(VideoRepository::class);
    
    expect($repo)->toBeInstanceOf(DatabaseVideoRepository::class);
});

it('resolves the flat-file repository when configured', function () {
    config(['statamic-ytlistr.driver' => 'flat_file']);
    
    $repo = app(VideoRepository::class);
    
    expect($repo)->toBeInstanceOf(YamlVideoRepository::class);
});

it('can store and retrieve videos cleanly using the yaml repository engine', function () {
    config(['statamic-ytlistr.driver' => 'flat_file']);
    $repo = app(VideoRepository::class);

    // 1. Assert initial state is clean
    expect($repo->count())->toBe(0);

    // 2. Write a mock payload map down into the yaml space
    $repo->updateOrCreate('mock_video_vector', [
        'video_id' => 'mock_video_vector',
        'title' => 'Testing Dynamic Fluid Storage Layers',
        'published_at' => '2026-07-10 12:00:00',
        'view_count' => 500,
    ]);

    // 3. Evaluate assertions against the flat-file disk state
    expect($repo->count())->toBe(1);
    
    $latest = $repo->latest();
    expect(data_get($latest, 'title'))->toBe('Testing Dynamic Fluid Storage Layers');
    expect((int) data_get($latest, 'view_count'))->toBe(500);
});