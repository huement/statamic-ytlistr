<?php

use Huement\StatamicYtListr\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Bind Pest to your custom Statamic test case environment globally
pest()->extend(TestCase::class)
  ->use(RefreshDatabase::class) // Wipes the database memory between test cycles
  ->in('Feature');
