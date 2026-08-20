<?php

namespace App\Providers;

use App\Models\Download;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Saroven\Reportify\Events\ExportCompleted;
use Saroven\Reportify\Events\ExportFailed;
use Saroven\Reportify\Events\ExportStarted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Create processing record when ExportStarted fires
        Event::listen(function (ExportStarted $event) {
            Log::info("Export '{$event->title}' has started processing.");

            Download::create([
                'user_id' => $event->userId ?: null,
                'title' => $event->title,
                'format' => strtoupper($event->exportFormat),
                'status' => 'processing',
            ]);
        });

        // 2. Update record to completed when ExportCompleted fires
        Event::listen(function (ExportCompleted $event) {
            $download = Download::where('title', $event->title)
                ->where('format', strtoupper($event->exportFormat))
                ->where('status', 'processing')
                ->latest('id')
                ->first();

            if ($download) {
                $download->update([
                    'file_path' => $event->filePath,
                    'status' => 'completed',
                ]);
            } else {
                Download::create([
                    'user_id' => $event->userId ?: null,
                    'title' => $event->title,
                    'format' => strtoupper($event->exportFormat),
                    'file_path' => $event->filePath,
                    'status' => 'completed',
                ]);
            }
        });

        // 3. Update record to failed when ExportFailed fires
        Event::listen(function (ExportFailed $event) {
            $download = Download::where('title', $event->title)
                ->where('format', strtoupper($event->exportFormat))
                ->where('status', 'processing')
                ->latest('id')
                ->first();

            if ($download) {
                $download->update([
                    'status' => 'failed',
                    'error' => $event->errorMessage,
                ]);
            } else {
                Download::create([
                    'user_id' => $event->userId ?: null,
                    'title' => $event->title,
                    'format' => strtoupper($event->exportFormat),
                    'status' => 'failed',
                    'error' => $event->errorMessage,
                ]);
            }
        });
    }
}
