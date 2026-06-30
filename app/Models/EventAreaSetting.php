<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAreaSetting extends Model
{
    protected $fillable = [
        'accreditation_event_id',
        'area_id',
        'is_avp_hidden',
    ];

    protected $casts = [
        'is_avp_hidden' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(AccreditationEvent::class, 'accreditation_event_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
