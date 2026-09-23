# Laravel Reportify Quick Start & Demo Guide 🚀

Welcome to **Laravel Reportify**! This repository serves as a complete working demo and quick-start reference for integrating [`saroven/laravel-reportify`](https://packagist.org/packages/saroven/laravel-reportify) into any Laravel application.

Reportify simplifies multi-format document exporting (**PDF**, **Excel**, **CSV**, **TXT**, and **ZIP**) with minimal boilerplate, native queue integration, event-driven background processing, and built-in Blade components.

---

## 🚀 Step-by-Step Integration Guide for New Users

### 1. Installation

Install the package via Composer:

```bash
composer require saroven/laravel-reportify:^1.0.5
```

Publish configuration and views (optional):

```bash
php artisan vendor:publish --tag=reportify-config
php artisan vendor:publish --tag=reportify-views
```

---

### 2. Make Any Controller Exportable

Implement the `Reportable` interface and use the `HasReportify` trait on your controller:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Saroven\Reportify\Contracts\Reportable;
use Saroven\Reportify\Traits\HasReportify;
use App\Exports\UserExport;
use App\Models\User;

class UserController extends Controller implements Reportable
{
    use HasReportify;

    public function index(Request $request)
    {
        // 1-line export handler (PDF Stream, PDF Chunk, Excel, CSV, TXT)
        if ($request->has('export')) {
            $view = in_array($request->get('export'), ['pdfStream', 'pdf', 'pdfChunk']) ? 'reports.users-pdf' : null;

            $additionalData = [];
            if ($request->filled('header_margin')) {
                $additionalData['headerMargin'] = (int) $request->input('header_margin');
            }
            if ($request->filled('additional_header_margin')) {
                $additionalData['additionalHeaderMargin'] = (int) $request->input('additional_header_margin');
            }

            return $this->exportReport(
                $request,
                'User Directory Report',
                view: $view,
                additionalData: $additionalData,
                dataProvider: UserExport::class
            );
        }

        $users = User::latest('id')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function getExportData(array $payload, string $exportType, int|string|null $userId = null): mixed
    {
        return User::query()->get();
    }
}
```

---

### 3. Generate Dedicated Export Classes

Generate clean data provider classes using the Artisan generator command:

```bash
php artisan reportify:make UserExport
```

This creates `app/Exports/UserExport.php`:

```php
namespace App\Exports;

use App\Models\User;
use Saroven\Reportify\Contracts\Reportable;

class UserExport implements Reportable
{
    public function getExportData(array $payload, string $exportType, int|string|null $userId = null): mixed
    {
        $query = User::query();

        if (!empty($payload['search'])) {
            $search = $payload['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->get()->map(function (User $user) {
            return [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Role' => $user->role,
                'Department' => $user->department ?? '-',
                'Phone' => $user->phone ?? '-',
                'Status' => $user->status,
                'Created At' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-',
            ];
        });
    }
}
```

---

### 4. Drop-in Export Buttons & Scripts in Blade

Include the drop-in export buttons component `<x-reportify-buttons />` and helper scripts `<x-reportify-scripts />` in your Blade views:

```html
<!-- Drop-in Export Action Buttons Component -->
<x-reportify-buttons
    :pdfStream="['url' => '#', 'onClick' => 'exportLinkRedirectWithUrlParams(event, {type: `pdfStream`})']"
    :pdf="['url' => '#', 'onClick' => 'exportLinkRedirectWithUrlParams(event, {type: `pdf`})']"
    :excel="['url' => '#', 'onClick' => 'exportLinkRedirectWithUrlParams(event, {type: `excel`})']"
    :csv="['url' => '#', 'onClick' => 'exportLinkRedirectWithUrlParams(event, {type: `csv`})']"
    :txt="['url' => '#', 'onClick' => 'exportLinkRedirectWithUrlParams(event, {type: `txt`})']"
/>

<!-- Include helper scripts at the end of the view body -->
<x-reportify-scripts />
```

---

### 5. Build a Download Manager with Event Listeners

Reportify dispatches lifecycle events during background exports (`ExportStarted`, `ExportCompleted`, `ExportFailed`). Register listeners in `AppServiceProvider.php` to track file processing states and persist downloads:

```php
namespace App\Providers;

use App\Models\Download;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Saroven\Reportify\Events\ExportStarted;
use Saroven\Reportify\Events\ExportCompleted;
use Saroven\Reportify\Events\ExportFailed;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Export Started (Set processing status with unique exportId)
        Event::listen(function (ExportStarted $event) {
            Download::create([
                'export_id' => $event->exportId,
                'user_id'   => $event->userId ?: null,
                'title'     => $event->title,
                'format'    => strtoupper($event->exportFormat),
                'status'    => 'processing',
            ]);
        });

        // 2. Export Completed (Match by exportId to eliminate race conditions)
        Event::listen(function (ExportCompleted $event) {
            $download = $event->exportId
                ? Download::where('export_id', $event->exportId)->first()
                : Download::where('title', $event->title)->where('status', 'processing')->latest('id')->first();

            if ($download) {
                $download->update([
                    'file_path' => $event->filePath,
                    'status'    => 'completed',
                ]);
            }
        });

        // 3. Export Failed (Update status with error message)
        Event::listen(function (ExportFailed $event) {
            $download = $event->exportId
                ? Download::where('export_id', $event->exportId)->first()
                : Download::where('title', $event->title)->where('status', 'processing')->latest('id')->first();

            if ($download) {
                $download->update([
                    'status' => 'failed',
                    'error'  => $event->errorMessage,
                ]);
            }
        });
    }
}
```


---

### 6. API Support & Header Margin Fine-Tuning

- **Automatic JSON Responses**: When called with `Accept: application/json`, `exportReport()` automatically returns a JSON response instead of a web redirect:
  ```json
  {
      "message": "Export for 'User Directory Report' is being processed. Check Download Manager."
  }
  ```
- **Header Margin Control**: Fine-tune PDF margins per-report via `$additionalData`:
  - `headerMargin` (int): Hard override for the PDF top margin in mm (bypasses auto-calculation).
  - `additionalHeaderMargin` (int): Additive offset applied on top of the auto-calculated or overridden margin (supports negative values to reduce margin).
  - Global default can be configured in `config/reportify.php` under `'mpdf.default_header_margin' => 28`.

---

## 🧪 Running Tests

Run the Pest test suite:

```bash
vendor/bin/pest
```

---

## 📜 License

The MIT License (MIT).
