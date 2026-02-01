<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function store(array $data, UploadedFile $file): File
    {
        $path = $this->storeFile($file);

        return File::create([
            'title' => $data['title'],
            'college_id' => $data['area_id'],
            'program_id' => $data['area_id'],
            'level' => $data['level'],
            'area_id' => $data['area_id'],
            'uploaded_by' => $data['uploaded_by'],
            'expiration' => $data['expiration'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_extension' => $file->getClientOriginalExtension(),
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