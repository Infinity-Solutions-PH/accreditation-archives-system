<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class AccreditationEvent extends Model
{
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
            $event->slug = Str::slug($event->title);
        });

        static::updating(function ($event) {
            $event->slug = Str::slug($event->title);
        });
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
                    ->withPivot('area_id', 'shared_by')
                    ->withTimestamps();
    }

    public function accreditors()
    {
        return $this->belongsToMany(Accreditor::class, 'accreditor_event_access')
                    ->withTimestamps();
    }
}
