<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function index()
    {
        $downloads = Download::latest('id')->paginate(10);

        return view('downloads.index', compact('downloads'));
    }

    public function download(Download $download)
    {
        $disk = config('reportify.storage_disk', 'public');

        if (!$download->file_path || !Storage::disk($disk)->exists($download->file_path)) {
            return back()->with('error', 'File not found or has expired.');
        }

        return Storage::disk($disk)->download($download->file_path);
    }

    public function destroy(Download $download)
    {
        $disk = config('reportify.storage_disk', 'public');

        if ($download->file_path && Storage::disk($disk)->exists($download->file_path)) {
            Storage::disk($disk)->delete($download->file_path);
        }

        $download->delete();

        return redirect()->route('downloads.index')->with('success', 'Download record removed successfully.');
    }
}
