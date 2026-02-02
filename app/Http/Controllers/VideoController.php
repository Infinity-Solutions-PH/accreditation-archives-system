<?php

namespace App\Http\Controllers;

use App\Models\File;

class VideoController extends Controller
{
    public function watch($file)
    {
        $user = auth()->user();
        
        // Check if user is authorized to view this video
        $videoRecord = File::where('id', $file)->firstOrFail();

        // if (!$user || !$user->can('view', $videoRecord)) {
        //     abort(403);
        // }

        // $path = storage_path('app/private_videos/' . $file);

        // if (!file_exists($path)) {
        //     abort(404);
        // }

        // return response()->file($path, [
        //     'Content-Type' => mime_content_type($path),
        //     'Content-Disposition' => 'inline',
        // ]);
        // if (!$user || !$user->can('view', $videoRecord)) {
        //     abort(403);
        // }

        $path = storage_path('app/' . $videoRecord->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $file, [
            'Content-Type' => mime_content_type($path),
            'Content-Disposition' => 'inline',
        ]);
    }
}
