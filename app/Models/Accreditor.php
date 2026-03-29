<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Accreditor extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'expires_at', 
        'college_id', 'program_id', 'level', 'created_by',
        'accreditation_event_id'
    ];

    public function accreditationEvent()
    {
        return $this->belongsTo(AccreditationEvent::class);
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
