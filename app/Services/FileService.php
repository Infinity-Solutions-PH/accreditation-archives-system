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
            'college' => $data['college'],
            'program' => $data['program'],
            'level' => $data['level'],
            'expiration' => $data['expiration'],

            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
        ]);
    }

    protected function storeFile(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs(
            'files',
            $filename,
            'public'
        );
    }
}