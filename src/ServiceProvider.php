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
	  \Statamic\Facades\CP\Nav::extend(function ($nav) {
          $nav->tools('YouTube Listr')
              ->icon('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h9a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>')
              ->url(cp_route('statamic-ytlistr.index'));
      });
  }

  protected function bootAddonTags(): void
  {
    YtListr::register();
  }
}
