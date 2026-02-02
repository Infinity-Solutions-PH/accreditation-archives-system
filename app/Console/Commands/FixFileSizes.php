<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixFileSizes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-file-sizes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all files and update file_size column if exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting file size check...');

        $files = File::all();

        $bar = $this->output->createProgressBar($files->count());
        $bar->start();

        foreach ($files as $file) {
            if ($file->path && Storage::exists($file->path)) {
                $size = Storage::size($file->path);

                $file->update([
                    'size' => $size,
                ]);
            } else {
                $this->warn("File missing: ID {$file->id}, Path: {$file->path}");
                $file->update([
                    'size' => 0,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nDone!");
    }
}
