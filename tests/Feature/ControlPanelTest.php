<?php

use Statamic\Facades\User;

it('redirects unauthenticated guest vectors away from the dashboard', function () {
  // Attempt to access your index route as an anonymous visitor
  $this->get(cp_route('statamic-ytlistr.index'))
    ->assertRedirect(cp_route('login'));
});

it('allows authenticated super admins to securely access the video dashboard index', function () {
  // 1. Arrange: Create a mock Statamic super-user programmatically
  $user = User::make()
    ->email('admin@huement.test')
    ->makeSuper()
    ->save();

  // 2. Act & Assert: Log in as that user and hit your index controller route
  $this->actingAs($user)
    ->get(cp_route('statamic-ytlistr.index'))
    ->assertOk()
    ->assertViewIs('statamic-ytlistr::cp.index') // FIXED: Added cp. prefix
    ->assertSee('YouTube Listr');
});
