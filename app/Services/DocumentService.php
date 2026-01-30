<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class DocumentService
{
    public function store(array $data, UploadedFile $file): Document
    {
        $path = $this->storeFile($file);

        return Document::create([
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
            'documents',
            $filename,
            'public'
        );
    }
}