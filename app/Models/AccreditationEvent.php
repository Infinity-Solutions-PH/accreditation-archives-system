<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccreditationEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'slug', 'description', 'college_id', 'program_id', 
        'level', 'expires_at', 'status', 'created_by'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($event) {
            $event->slug = static::generateUniqueSlug($event->title);
        });

        static::updating(function ($event) {
            if ($event->isDirty('title')) {
                $event->slug = static::generateUniqueSlug($event->title, $event->id);
            }
        });
    }

    public static function generateUniqueSlug($title, $ignoreId = 0)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'accreditation_event_files')
                    ->withPivot('area_id', 'shared_by', 'subfolder', 'parameter', 'parameter_folder')
                    ->withTimestamps();
    }

    public function accreditors()
    {
        return $this->belongsToMany(Accreditor::class, 'accreditor_event_access')
                    ->withTimestamps();
    }
}
