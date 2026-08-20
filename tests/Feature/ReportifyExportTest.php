<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'Admin',
        'status' => 'Active',
    ]);
});

it('streams pdf report via reportify', function () {
    $response = $this->get('/users?export=pdfStream');

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

it('processes chunked pdf report export via reportify', function () {
    $response = $this->get('/users?export=pdfChunk');

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('processes excel report export via reportify', function () {
    $response = $this->get('/users?export=excel');

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('processes csv report export via reportify', function () {
    $response = $this->get('/users?export=csv');

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('processes txt report export via reportify', function () {
    $response = $this->get('/users?export=txt');

    $response->assertRedirect();
    $response->assertSessionHas('success');
});
