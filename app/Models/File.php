<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Accreditor;
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
        'path',
        'original_filename',
        'extension',
        'size',
        'tmp_id',
        'uploaded_by',
        'status',
        'is_general'
    ];

    protected $casts = [
        'expiration' => 'date',
        'is_general' => 'boolean',
    ];

    protected $appends = [
        'size',
        'size_human',
        'created_at_clean',
        'created_at_timeago',
        'updated_at_timeago'
    ];

    // Delete temp file when record deleted
    protected static function booted()
    {
        static::deleting(function ($file) {
            if ($file->path && Storage::exists($file->path)) {
                Storage::delete($file->path);
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

    public function scopeAccessibleBy($query, $user)
    {
        if (!$user) {
            return $query->where('is_general', true);
        }

        if ($user instanceof User) {
            if ($user->hasRole('admin') || $user->hasRole('ido_staff')) {
                return $query; // Admin and IDO staff see all
            }

            return $query->where(function ($q) use ($user) {
                $q->where('is_general', true);

                if ($user->hasRole('college_officer')) {
                    $q->orWhere('college_id', $user->college_id);
                }

                if ($user->hasRole('taskforce')) {
                    $q->orWhere(function ($subQ) use ($user) {
                        $subQ->where('college_id', $user->college_id)
                             ->where('program_id', $user->program_id);
                    });
                }
            });
        }

        if ($user instanceof Accreditor) {
            return $query->where(function ($q) use ($user) {
                $q->where('is_general', true)
                  ->orWhere(function ($subQ) use ($user) {
                      $subQ->where('college_id', $user->college_id)
                           ->where('program_id', $user->program_id);
                      if ($user->level) {
                          $subQ->where('level', $user->level);
                      }
                  });
            });
        }

        return $query->where('is_general', true);
    }

    public function getSizeAttribute(): int
    {
        return (int) ($this->attributes['size'] ?? 0);
    }

    public function getSizeHumanAttribute(): string
    {
        $bytes = $this->size ?? 0;

        return match (true) {
            $bytes >= 1073741824 => number_format($bytes / 1073741824, 2) . ' GB',
            $bytes >= 1048576    => number_format($bytes / 1048576, 2) . ' MB',
            $bytes >= 1024       => number_format($bytes / 1024, 2) . ' KB',
            default              => $bytes . ' bytes',
        };
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
