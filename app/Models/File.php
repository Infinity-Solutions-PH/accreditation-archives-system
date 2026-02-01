<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
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
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
