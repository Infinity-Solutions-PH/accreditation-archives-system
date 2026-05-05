<?php

namespace App\Console\Commands;

use App\Models\Accreditor;
use Illuminate\Console\Command;

class DeactivateExpiredAccreditors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accreditors:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deactivate accreditor accounts that have passed their expiration date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Accreditor::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['is_active' => false]);

        $this->info("Successfully deactivated {$count} expired accreditor accounts.");
    }
}
