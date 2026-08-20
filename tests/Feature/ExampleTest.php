<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects home to users list', function () {
    $response = $this->get('/');

    $response->assertRedirect('/users');
});

it('renders user directory page successfully', function () {
    $response = $this->get('/users');

    $response->assertStatus(200);
    $response->assertSee('User List');
});
