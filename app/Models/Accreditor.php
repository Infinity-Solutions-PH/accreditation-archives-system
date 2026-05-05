<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Accreditor extends Authenticatable
{
    use Notifiable, SoftDeletes;


    protected $fillable = [
        'name', 'email', 'password', 'expires_at', 
        'college_id', 'program_id', 'level', 'created_by',
        'is_active', 'role_status'
    ];

    public function events()
    {
        return $this->belongsToMany(AccreditationEvent::class, 'accreditor_event_access')
                    ->withTimestamps();
    }

    public function googleInfo()
    {
        return $this->hasOne(GoogleUserInfo::class);
    }

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'password' => 'hashed',
        ];
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
}
