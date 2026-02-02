<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function watch(File $file) {
        $user = auth()->user();

        if(!$file) return abort(403);

        $path = Storage::path($file->path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->streamDownload(
            fn () => readfile($path),
            basename($path),
            [
                'Content-Type' => mime_content_type($path),
                'Content-Disposition' => 'inline',
            ]
        );
    }
}
