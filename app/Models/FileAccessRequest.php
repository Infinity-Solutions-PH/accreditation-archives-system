<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAccessRequest extends Model
{
    protected $fillable = [
        'file_id', 'user_id', 'status', 'reason', 'handled_by', 'handled_at'
    ];

    protected $casts = [
        'handled_at' => 'datetime'
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
