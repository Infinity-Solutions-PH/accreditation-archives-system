<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'commentable_id', 'commentable_type', 'accreditation_event_id', 
        'area_id', 'parent_id', 'content'
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function accreditationEvent()
    {
        return $this->belongsTo(AccreditationEvent::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
