<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChunkedUploadService
{
    public function createTemporaryMetadata(array $data): Document
    {
        $tmpId = Str::uuid()->toString();

        $document = Document::create([
            'title' => $data['metadata']['title'] ?? "Untitled-Document",
            'description' => $data['metadata']['description'] ?? null,
            'original_filename' => $data['filename'],
            'tmp_id' => $tmpId,
            'status' => 'uploading',
        ]);

        Storage::makeDirectory("tmp/$tmpId");

        return $document;
    }

    public function saveChunk(Document $document, UploadedFile $chunk, int $index): void
    {
        Storage::putFileAs("tmp/{$document->tmp_id}", $chunk, "chunk-$index");
    }

    public function mergeChunks(Document $document): void
    {
        $chunks = Storage::files("tmp/{$document->tmp_id}");
        usort($chunks, fn($a, $b) => strcmp($a, $b));

        $finalFilename = Str::uuid() . '.' . pathinfo($document->original_filename, PATHINFO_EXTENSION);
        $finalPath = "private/documents/$finalFilename";

        $outStream = fopen(storage_path("app/$finalPath"), 'wb');

        foreach ($chunks as $chunkPath) {
            $chunkStream = fopen(storage_path("app/private/$chunkPath"), 'rb');
            stream_copy_to_stream($chunkStream, $outStream);
            fclose($chunkStream);
        }

        fclose($outStream);

        Storage::deleteDirectory("tmp/{$document->tmp_id}");

        $document->update([
            'file_path' => $finalPath,
            'status' => 'completed',
        ]);
    }

    public function updateMetadata(Document $document, array $metadata): void
    {
        $document->update([
            'title' => $metadata['title'] ?? $document->title,
            'description' => $metadata['description'] ?? $document->description,
        ]);
    }

    public function abort(Document $document): void
    {
        Storage::deleteDirectory("tmp/{$document->tmp_id}");
        $document->delete();
    }
}