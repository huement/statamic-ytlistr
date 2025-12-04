<?php

namespace Huement\StatamicYtListr;

use Huement\StatamicYtListr\Console\Commands\FetchLatestVideos;
use Huement\StatamicYtListr\Tags\YtListr;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Statamic;

class ServiceProvider extends BaseServiceProvider
{
  public function register(): void
  {
    $this->mergeConfigFrom(
      __DIR__ . '/../config/statamic-ytlistr.php',
      'statamic-ytlistr'
    );
  }

  public function boot(): void
  {
    $this->bootAddonConfig();
    $this->bootAddonViews();
    $this->bootAddonMigrations();
    $this->bootAddonRoutes();
    $this->bootAddonCommands();
    $this->bootAddonNav();
    $this->bootAddonTags();
  }

  protected function bootAddonConfig(): void
  {
    $this->publishes(
      [
        __DIR__ . '/../config/statamic-ytlistr.php' => config_path(
          'statamic-ytlistr.php'
        ),
      ],
      'statamic-ytlistr-config'
    );
  }

  protected function bootAddonViews(): void
  {
    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'statamic-ytlistr');

    $this->publishes(
      [
        __DIR__ . '/../resources/views' => resource_path(
          'views/vendor/statamic-ytlistr'
        ),
      ],
      'statamic-ytlistr-views'
    );
  }

  protected function bootAddonMigrations(): void
  {
    if ($this->app->runningInConsole()) {
      $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
  }

  protected function bootAddonRoutes(): void
  {
    Statamic::pushCpRoutes(function () {
      require __DIR__ . '/../routes/cp.php';
    });
  }

  protected function bootAddonCommands(): void
  {
    if ($this->app->runningInConsole()) {
      $this->commands([FetchLatestVideos::class]);
    }
  }

  protected function bootAddonNav(): void
  {
    Nav::extend(function ($nav) {
      $nav
        ->create('YouTube Listr')
        ->section('Tools')
        ->route('statamic-ytlistr.index') // <--- UPDATE THIS LINE
        ->icon('video');
    });
  }

  protected function bootAddonTags(): void
  {
    YtListr::register();
  }
}
