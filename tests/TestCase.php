<?php

namespace Huement\StatamicYtListr\Tests;

use Statamic\Testing\AddonTestCase;
use Huement\StatamicYtListr\ServiceProvider;

abstract class TestCase extends AddonTestCase
{
  // Tells Statamic which addon provider to boot up
  protected string $addonServiceProvider = ServiceProvider::class;

  /**
   * Load environment setup configurations if needed.
   */
  protected function getEnvironmentSetUp($app)
  {
    parent::getEnvironmentSetUp($app);

    $app['config']->set('app.key', 'base64:UT0p6Fbc637gMcYSkp6v1S87v3G68asGgsh6asD7as8=');
    $app['config']->set('statamic.editions.pro', true);
    // Setup an in-memory SQLite database for database-driven feature testing
    $app['config']->set('database.default', 'testbench');
    $app['config']->set('database.connections.testbench', [
      'driver' => 'sqlite',
      'database' => ':memory:',
      'prefix' => '',
    ]);
  }
}
