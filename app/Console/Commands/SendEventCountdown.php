<?php

namespace App\Console\Commands;

use App\Models\AccreditationEvent;
use App\Models\User;
use App\Notifications\EventCountdownNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventCountdown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-countdown';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily countdown notifications to participants of active accreditation events.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $activeEvents = AccreditationEvent::where('status', 'active')
            ->where('expires_at', '>', now())
            ->get();

        if ($activeEvents->isEmpty()) {
            $this->info('No active events found for countdown.');
            return;
        }

        foreach ($activeEvents as $event) {
            $daysLeft = (int) ceil(now()->diffInDays($event->expires_at, false));
            
            // Notify involved users
            $recipients = User::role(['ido_staff', 'college_officer', 'taskforce'])
                ->where(function($q) use ($event) {
                    // IDO Staff always get notified
                    $q->whereHas('roles', fn($r) => $r->where('name', 'ido_staff'))
                      ->orWhere(function($sub) use ($event) {
                          // Filter by college and program for officer/taskforce
                          $sub->where('college_id', $event->college_id)
                              ->where(function($inner) use ($event) {
                                  $inner->whereHas('roles', fn($r) => $r->where('name', 'college_officer'))
                                        ->orWhere(function($tf) use ($event) {
                                            $tf->whereHas('roles', fn($r) => $r->where('name', 'taskforce'))
                                               ->where('program_id', $event->program_id);
                                        });
                              });
                      });
                })
                ->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(new EventCountdownNotification($event, $daysLeft));
            }
        }

        $this->info('Event countdown notifications dispatched to ' . $activeEvents->count() . ' active events.');
    }
}
