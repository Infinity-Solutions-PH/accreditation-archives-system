<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class VideoStreamController extends Controller
{
    public function stream(Request $request, File $file)
    {
        // Authorization (policy recommended)
        // $this->authorize('view', $file);

        $path = storage_path('app/' . $file->file_path);

        abort_unless(file_exists($path), 404);

        $size = filesize($path);
        $mime = mime_content_type($path);
        $start = 0;
        $end = $size - 1;

        if ($request->hasHeader('Range')) {
            preg_match('/bytes=(\d+)-(\d*)/', $request->header('Range'), $matches);

            $start = intval($matches[1]);
            if (!empty($matches[2])) {
                $end = intval($matches[2]);
            }
        }

        $length = $end - $start + 1;

        return response()->stream(function () use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            echo fread($handle, $length);
            fclose($handle);
        }, 206, [
            'Content-Type' => $mime,
            'Content-Length' => $length,
            'Content-Range' => "bytes $start-$end/$size",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
