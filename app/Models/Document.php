<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected  $fillable = [
        'title',
        'description',
        'college',
        'program',
        'level',
        'expiration',
        'file_path',
        'original_filename',
        'tmp_id',
        'status',
    ];

    protected $casts = [
        'expiration' => 'date',
    ];

    // Delete temp file when record deleted
    protected static function booted()
    {
        static::deleting(function ($document) {
            if ($document->file_path && Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });
    }
}
