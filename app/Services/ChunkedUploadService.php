<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Notifications\FileUploadedNotification;

class ChunkedUploadService
{
    public function createTemporaryMetadata(array $data): File
    {
        $tmpId = Str::uuid()->toString();
        $user = auth()->user();

        $isGeneral = isset($data['metadata']['is_general']) ? (bool) $data['metadata']['is_general'] : true;

        $collegeId = $data['metadata']['college_id'] ?? ($user ? $user->college_id : null);
        $programId = $data['metadata']['program_id'] ?? ($user ? $user->program_id : null);

        if ($user) {
            if ($user->hasRole('taskforce')) {
                $collegeId = $user->college_id;
                $programId = $user->program_id;
            } elseif ($user->hasRole('college_officer')) {
                $collegeId = $user->college_id;
            }
        }

        if ($isGeneral) {
            $programId = null;
        }

        $file = File::create([
            'title' => $data['metadata']['title'] ?? "Untitled-File",
            'description' => $data['metadata']['description'] ?? null,
            'original_filename' => $data['filename'],
            'extension' => pathinfo($data['filename'], PATHINFO_EXTENSION),
            'tmp_id' => $tmpId,
            'status' => 'uploading',
            'uploaded_by' => $user->id ?? null,
            'college_id' => $collegeId,
            'program_id' => $programId,
            'is_general' => $isGeneral
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

        foreach ($chunks as $chunkPath) {
            $chunkStream = fopen(Storage::path($chunkPath), 'rb');
            stream_copy_to_stream($chunkStream, $outStream);
            fclose($chunkStream);
        }

        fclose($outStream);

        $fileSize = Storage::size($finalPath);

        Storage::deleteDirectory("tmp/{$file->tmp_id}");

        $file->update([
            'path' => $finalPath,
            'size' => $fileSize,
            'status' => 'completed'
        ]);
    }

    public function updateMetadata(File $file, array $metadata): void
    {
        $user = auth()->user();

        $isGeneral = isset($metadata['is_general']) ? (bool) $metadata['is_general'] : (bool) $file->is_general;
        $collegeId = $metadata['college_id'] ?? $file->college_id;

        if ($isGeneral) {
            $programId = null;
        } else {
            $programId = array_key_exists('program_id', $metadata) ? $metadata['program_id'] : $file->program_id;
        }

        if ($user) {
            if ($user->hasRole('taskforce')) {
                $collegeId = $user->college_id;
                $programId = $user->program_id;
            } elseif ($user->hasRole('college_officer')) {
                $collegeId = $user->college_id;
            }
        }

        $file->update([
            'title' => $metadata['title'] ?? $file->title,
            'description' => $metadata['description'] ?? $file->description,
            'college_id' => $collegeId,
            'program_id' => $programId,
            'area_id' => $metadata['area_id'] ?? $file->area_id,
            'level' => $metadata['level'] ?? $file->level,
            'is_general' => $isGeneral
        ]);

        // Notify if uploader is taskforce
        $uploader = $file->uploadedBy;
        if ($uploader && $uploader->hasRole('taskforce') && $file->status === 'completed') {
            $recipients = User::role(['college_officer', 'taskforce'])
                ->where('college_id', $file->college_id)
                ->where('id', '!=', $uploader->id)
                ->get();
            
            foreach ($recipients as $recipient) {
                $recipient->notify(new FileUploadedNotification($file));
            }
        }
    }

    public function abort(File $file): void
    {
        Storage::deleteDirectory("tmp/{$file->tmp_id}");
        $file->delete();
    }
}