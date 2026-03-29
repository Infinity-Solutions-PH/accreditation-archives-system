<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccreditationEvent extends Model
{
    protected $fillable = [
        'title', 'description', 'college_id', 'program_id', 
        'level', 'expires_at', 'status', 'created_by'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

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
        return $this->hasMany(Accreditor::class);
    }
}
