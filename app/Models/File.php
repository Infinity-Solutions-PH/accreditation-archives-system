<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected  $fillable = [
        'title',
        'description',
        'college_id',
        'program_id',
        'level',
        'area_id',
        'expiration',
        'file_path',
        'original_filename',
        'file_extension',
        'tmp_id',
        'uploaded_by',
        'status'
    ];

    protected $casts = [
        'expiration' => 'date',
    ];

    protected $appends = [
        'created_at_clean',
        'created_at_timeago',
        'updated_at_timeago'
    ];

    // Delete temp file when record deleted
    protected static function booted()
    {
        static::deleting(function ($file) {
            if ($file->file_path && Storage::exists($file->file_path)) {
                Storage::delete($file->file_path);
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

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getCreatedAtCleanAttribute(): string
    {
        return $this->created_at
            ? Carbon::parse($this->created_at)->format('F d, Y')
            : '';
    }

    // Created at timeago
    public function getCreatedAtTimeagoAttribute(): string
    {
        return $this->created_at
            ? Carbon::parse($this->created_at)->diffForHumans()
            : '';
    }

    // Updated at timeago
    public function getUpdatedAtTimeagoAttribute(): string
    {
        return $this->updated_at
            ? Carbon::parse($this->updated_at)->diffForHumans()
            : '';
    }
}
