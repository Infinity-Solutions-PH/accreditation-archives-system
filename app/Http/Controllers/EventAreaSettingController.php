<?php

namespace App\Http\Controllers;

use App\Models\AccreditationEvent;
use App\Models\Area;
use App\Models\EventAreaSetting;
use Illuminate\Http\Request;

class EventAreaSettingController extends Controller
{
    public function toggleAvpVisibility(Request $request, AccreditationEvent $event, Area $area)
    {
        $user = auth()->user();

        // Only IDO Staff, Admin, or College Officer can toggle
        if (!$user || !($user->hasRole('ido_staff') || $user->hasRole('admin') || $user->hasRole('college_officer'))) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $setting = EventAreaSetting::firstOrCreate(
            ['accreditation_event_id' => $event->id, 'area_id' => $area->id],
            ['is_avp_hidden' => false]
        );

        $setting->is_avp_hidden = !$setting->is_avp_hidden;
        $setting->save();

        return response()->json([
            'status' => 'success',
            'is_avp_hidden' => $setting->is_avp_hidden
        ]);
    }
}
