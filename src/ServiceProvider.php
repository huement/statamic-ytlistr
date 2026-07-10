<?php

namespace Huement\StatamicYtListr;

use Huement\StatamicYtListr\Console\Commands\FetchLatestVideos;
use Huement\StatamicYtListr\Tags\YtListr;
use Huement\StatamicYtListr\Repositories\VideoRepository;
use Huement\StatamicYtListr\Repositories\DatabaseVideoRepository;
use Huement\StatamicYtListr\Repositories\YamlVideoRepository;
use Statamic\Providers\AddonServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ServiceProvider extends AddonServiceProvider
{
  // 1. Let Statamic handle these automatically via properties!
  protected $commands = [
    FetchLatestVideos::class,
  ];

  protected $tags = [
    YtListr::class,
  ];

  protected $routes = [
    'cp' => __DIR__ . '/../routes/cp.php',
  ];

  // Register method for config merging
  public function register(): void
  {
    parent::register();

    $this->mergeConfigFrom(__DIR__ . '/../config/statamic-ytlistr.php', 'statamic-ytlistr');

    // Dynamically swap the data engine behind the scenes based on user config
    $this->app->bind(VideoRepository::class, function ($app) {
      $driver = config('statamic-ytlistr.driver', 'database');

      return $driver === 'flat_file'
        ? new YamlVideoRepository()
        : new DatabaseVideoRepository();
    });
  }

  /**
   * Statamic calls this automatically. No need for an overridden boot() or register() method!
   */
  public function bootAddon()
  {
    // 2. Handle configuration merging and asset publishing
    // $this->mergeConfigFrom(__DIR__ . '/../config/statamic-ytlistr.php', 'statamic-ytlistr');
    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'statamic-ytlistr');

    if ($this->app->runningInConsole()) {
      $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

      $this->publishes([
        __DIR__ . '/../config/statamic-ytlistr.php' => config_path('statamic-ytlistr.php'),
      ], 'statamic-ytlistr-config');

      $this->publishes([
        __DIR__ . '/../resources/views' => resource_path('views/vendor/statamic-ytlistr'),
      ], 'statamic-ytlistr-views');
    }

    // 3. Extend the Control Panel Navigation
    \Statamic\Facades\CP\Nav::extend(function ($nav) {
      $nav->tools('YouTube Listr')
        ->icon('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M8 5v14l11-7-11-7Z" /></svg>')
        ->url(cp_route('statamic-ytlistr.index'));
    });

    // 4. Task Scheduler Logic
    $this->app->booted(function () {
      // FIXED: Changed 'ytlistr' to 'statamic-ytlistr' to match your merge key
      if (!config('statamic-ytlistr.scheduler.enabled', true)) {
        return;
      }

      $schedule = $this->app->make(Schedule::class);

      // This references the actual signature declared inside FetchLatestVideos
      $command = $schedule->command('ytlistr:fetch');

      $frequency = config('statamic-ytlistr.scheduler.frequency', 'daily');

      switch ($frequency) {
        case 'cron':
          $cronExpression = config('statamic-ytlistr.scheduler.cron', '0 0 * * *');
          $command->cron($cronExpression);
          break;

        case 'hourly':
          $command->hourly();
          break;

        case 'daily':
        default:
          $time = config('statamic-ytlistr.scheduler.time', '00:00');
          $command->dailyAt($time);
          break;
      }
    });
  }
}
