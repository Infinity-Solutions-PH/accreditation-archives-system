<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ChunkedUploadService;
use App\Http\Requests\UploadChunkRequest;
use App\Http\Requests\CreateTempFileRequest;

class FileController extends Controller
{
    public function __construct(
        protected ChunkedUploadService $service
    ) {
    }

    private function checkActive() {
        /** @var \App\Models\User|null $user */
        $user = auth('web')->user();
        if ($user && $user->is_active === false) {
            abort(403, 'Inactive users cannot upload or modify files.');
        }
    }

    // 1️⃣ Create temporary metadata
    public function temp(CreateTempFileRequest $request)
    {
        $this->checkActive();
        $file = $this->service->createTemporaryMetadata($request->validated());

        return response()->json(['tmp_id' => $file->tmp_id]);
    }

    // 2️⃣ Upload chunk
    public function uploadChunk(UploadChunkRequest $request)
    {
        $this->checkActive();
        $file = File::where('tmp_id', $request->tmp_id)->firstOrFail();
        $this->service->saveChunk($file, $request->file('chunk'), $request->index);

        return response()->json(['status' => 'ok']);
    }

    // 3️⃣ Complete upload
    public function complete(Request $request)
    {
        $this->checkActive();
        $request->validate(['tmp_id' => 'required|string|exists:files,tmp_id']);

        $file = File::where('tmp_id', $request->tmp_id)->firstOrFail();
        $this->service->mergeChunks($file);

        return response()->json(['status' => 'completed']);
    }

    // 4️⃣ Update metadata
    public function updateMetadata(Request $request)
    {
        $this->checkActive();
        $request->validate([
            'tmp_id' => 'required|string|exists:files,tmp_id',
            'metadata.title' => 'nullable|string|max:255',
            'metadata.description' => 'nullable|string|max:1000',
            'metadata.college_id' => ['nullable', 'exists:colleges,id'],
            'metadata.program_id' => ['nullable', 'exists:programs,id'],
            'metadata.area_id' => ['nullable', 'exists:areas,id'],
            'metadata.level' => ['nullable', 'int'],
            'metadata.is_general' => ['nullable', 'boolean']
        ]);

        $file = File::where('tmp_id', $request->tmp_id)->firstOrFail();
        $this->service->updateMetadata($file, $request->metadata);

        return response()->json(['status' => 'ok']);
    }

    // 5️⃣ Abort upload
    public function abort(Request $request)
    {
        $this->checkActive();
        $request->validate(['tmp_id' => 'required|string|exists:files,tmp_id']);

        $file = File::where('tmp_id', $request->tmp_id)->first();
        if ($file) {
            $this->service->abort($file);
        }

        return response()->json(['status' => 'aborted']);
    }

    /**
     * Inline file viewer support
     */
    public function view(File $file)
    {
        // Enforce the same accessibility scope
        $isAccessible = File::accessibleBy(auth()->user())->where('id', $file->id)->exists();
        abort_unless($isAccessible, 403, 'You do not have permission to view this file.');

        $cleanPath = str_replace('private/', '', $file->path);
        
        if (!Storage::disk('local')->exists($cleanPath)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('local')->response($cleanPath, $file->original_filename, [
            'Content-Disposition' => 'inline; filename="' . $file->original_filename . '"',
            'Content-Type' => Storage::disk('local')->mimeType($cleanPath)
        ]);
    }

    /**
     * Standard force-download support
     */
    public function download(File $file)
    {
        $isAccessible = File::accessibleBy(auth()->user())->where('id', $file->id)->exists();
        abort_unless($isAccessible, 403, 'You do not have permission to download this file.');

        $cleanPath = str_replace('private/', '', $file->path);

        if (!Storage::disk('local')->exists($cleanPath)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('local')->download($cleanPath, $file->original_filename);
    }
}
