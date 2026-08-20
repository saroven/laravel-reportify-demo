<?php

use App\Models\Download;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Saroven\Reportify\Events\ExportCompleted;
use Saroven\Reportify\Events\ExportFailed;
use Saroven\Reportify\Events\ExportStarted;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    User::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

it('renders download manager index page', function () {
    $response = $this->get('/downloads');

    $response->assertStatus(200);
    $response->assertSee('Download Manager');
});

it('creates processing download record on ExportStarted event', function () {
    ExportStarted::dispatch(1, 'User Directory Report', 'excel', []);

    $this->assertDatabaseHas('downloads', [
        'title' => 'User Directory Report',
        'format' => 'EXCEL',
        'status' => 'processing',
    ]);
});

it('updates download record to completed on ExportCompleted event', function () {
    ExportStarted::dispatch(1, 'User Directory Report', 'excel', []);
    ExportCompleted::dispatch(1, 'User Directory Report', 'excel', 'exports/excel/user-report.xlsx', []);

    $this->assertDatabaseHas('downloads', [
        'title' => 'User Directory Report',
        'format' => 'EXCEL',
        'status' => 'completed',
        'file_path' => 'exports/excel/user-report.xlsx',
    ]);
});

it('updates download record to failed on ExportFailed event', function () {
    ExportStarted::dispatch(1, 'User Directory Report', 'excel', []);
    ExportFailed::dispatch(1, 'User Directory Report', 'excel', 'Database connection lost', []);

    $this->assertDatabaseHas('downloads', [
        'title' => 'User Directory Report',
        'format' => 'EXCEL',
        'status' => 'failed',
        'error' => 'Database connection lost',
    ]);
});

it('records download entry when triggering report export', function () {
    $this->get('/users?export=excel');

    $this->assertDatabaseHas('downloads', [
        'title' => 'User Directory Report',
        'format' => 'EXCEL',
        'status' => 'completed',
    ]);
});

it('downloads file when requested', function () {
    Storage::disk('public')->put('exports/excel/sample.xlsx', 'dummy-content');

    $download = Download::create([
        'title' => 'Sample Report',
        'format' => 'EXCEL',
        'file_path' => 'exports/excel/sample.xlsx',
        'status' => 'completed',
    ]);

    $response = $this->get("/downloads/{$download->id}/file");

    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});

it('deletes download record and file', function () {
    Storage::disk('public')->put('exports/excel/sample.xlsx', 'dummy-content');

    $download = Download::create([
        'title' => 'Sample Report',
        'format' => 'EXCEL',
        'file_path' => 'exports/excel/sample.xlsx',
        'status' => 'completed',
    ]);

    $response = $this->delete("/downloads/{$download->id}");

    $response->assertRedirect('/downloads');
    $this->assertDatabaseMissing('downloads', ['id' => $download->id]);
    Storage::disk('public')->assertMissing('exports/excel/sample.xlsx');
});
