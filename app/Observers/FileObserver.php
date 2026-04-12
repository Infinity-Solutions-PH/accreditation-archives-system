<?php

namespace App\Observers;

use App\Models\File;
use App\Models\User;
use App\Notifications\FileAlteredInEventNotification;

class FileObserver
{
    /**
     * Handle the File "updated" event.
     */
    public function updated(File $file): void
    {
        // Only notify if title or description changed
        if ($file->isDirty(['title', 'description'])) {
            $user = auth()->user();
            if (!$user) return;

            // Find all active accreditation events this file is part of
            $events = $file->accreditationEvents()
                ->where('accreditation_events.status', 'active')
                ->get();

            foreach ($events as $event) {
                // Determine recipients (Event creator + program taskforce/officers)
                $recipients = User::role(['admin', 'ido_staff', 'college_officer', 'taskforce'])
                    ->where(function($q) use ($event) {
                        $q->where('id', $event->created_by)
                          ->orWhere(function($sub) use ($event) {
                              $sub->where('college_id', $event->college_id)
                                  ->whereHas('roles', fn($r) => $r->whereIn('name', ['college_officer', 'taskforce']));
                          });
                    })
                    ->where('id', '!=', $user->id)
                    ->distinct()
                    ->get();

                foreach ($recipients as $recipient) {
                    $recipient->notify(new FileAlteredInEventNotification($event, $file, $user->name));
                }
            }
        }
    }
}
