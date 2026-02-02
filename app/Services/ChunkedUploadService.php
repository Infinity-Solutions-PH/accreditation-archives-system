<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChunkedUploadService
{
    public function createTemporaryMetadata(array $data): File
    {
        $tmpId = Str::uuid()->toString();

        $file = File::create([
            'title' => $data['metadata']['title'] ?? "Untitled-File",
            'description' => $data['metadata']['description'] ?? null,
            'original_filename' => $data['filename'],
            'file_extension' => pathinfo($data['filename'], PATHINFO_EXTENSION),
            'tmp_id' => $tmpId,
            'status' => 'uploading',
            'uploaded_by' => auth()->id() ?? null
        ]);

        Storage::makeDirectory("tmp/$tmpId");

        return $file;
    }

    public function saveChunk(File $file, UploadedFile $chunk, int $index): void
    {
        Storage::putFileAs("tmp/{$file->tmp_id}", $chunk, "chunk-$index");
    }

    public function mergeChunks(File $file): void
    {
        $chunks = Storage::files("tmp/{$file->tmp_id}");

        // Natural numeric sorting
        natsort($chunks);
        $chunks = array_values($chunks);

        $finalFilename = Str::uuid() . '.' . pathinfo($file->original_filename, PATHINFO_EXTENSION);
        $finalPath = "files/$finalFilename";

        $outStream = fopen(Storage::path($finalPath), 'wb');

        Log::info(Storage::path($finalPath));

        foreach ($chunks as $chunkPath) {
            $chunkStream = fopen(Storage::path($chunkPath), 'rb');
            stream_copy_to_stream($chunkStream, $outStream);
            fclose($chunkStream);
        }

        fclose($outStream);

        Storage::deleteDirectory("tmp/{$file->tmp_id}");

        $file->update([
            'file_path' => 'private/' . $finalPath,
            'status' => 'completed',
        ]);
    }

    public function updateMetadata(File $file, array $metadata): void
    {
        $file->update([
            'title' => $metadata['title'] ?? $file->title,
            'description' => $metadata['description'] ?? $file->description,
            'college_id' => $metadata['college_id'] ?? $file->college_id,
            'program_id' => $metadata['program_id'] ?? $file->program_id,
            'area_id' => $metadata['area_id'] ?? $file->area_id,
            'level' => $metadata['level'] ?? $file->level
        ]);
    }

    public function abort(File $file): void
    {
        Storage::deleteDirectory("tmp/{$file->tmp_id}");
        $file->delete();
    }
}