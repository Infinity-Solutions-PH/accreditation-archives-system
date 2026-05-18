<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function store(array $data, UploadedFile $file): File
    {
        $path = $this->storeFile($file);

        $user = isset($data['uploaded_by']) ? User::find($data['uploaded_by']) : auth()->user();

        $collegeId = $data['college_id'] ?? ($user ? $user->college_id : null);
        $programId = $data['program_id'] ?? ($user ? $user->program_id : null);

        if ($user) {
            if ($user->hasRole('taskforce')) {
                $collegeId = $user->college_id;
                $programId = $user->program_id;
            } elseif ($user->hasRole('college_officer')) {
                $collegeId = $user->college_id;
            }
        }

        // Validate the role requirements
        if ($user && $user->hasRole('taskforce')) {
            if (empty($collegeId) || empty($programId)) {
                throw new \InvalidArgumentException('When uploading files as taskforce role, the file must have a college_id and program_id.');
            }
        } elseif ($user && $user->hasRole('college_officer')) {
            if (empty($collegeId)) {
                throw new \InvalidArgumentException('On college_id officer, the file must have a college_id.');
            }
        }

        return File::create([
            'title' => $data['title'],
            'college_id' => $collegeId,
            'program_id' => $programId,
            'level' => $data['level'] ?? null,
            'area_id' => $data['area_id'] ?? null,
            'uploaded_by' => $user?->id ?? $data['uploaded_by'] ?? null,
            'expiration' => $data['expiration'] ?? null,
            'path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
        ]);
    }

    protected function storeFile(UploadedFile $file): string
    {
        $fileExtenstion = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $fileExtenstion;

        return $file->storeAs(
            'files',
            $filename,
            'public'
        );
    }
}