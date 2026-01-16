<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $normalized = str_replace('\\', '/', $name);
        $path = app_path("Services/{$normalized}.php");

        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        $className = class_basename($normalized);

        // Prevent overwriting existing files
        if (File::exists($path)) {
            $this->error("Service {$name} already exists!");
            return Command::FAILURE;
        }

        $namespace = 'App\\Services';
        $subPath = trim(str_replace('/', '\\', dirname($normalized)), '\\');
        if ($subPath !== '.') {
            $namespace .= '\\' . $subPath;
        }

        // File content stub
        $stub = <<<PHP
        <?php

        namespace {$namespace};

        class {$className}
        {
            public function __construct()
            {
                //
            }
        }
        PHP;

        // Write the file
        File::put($path, $stub);

        $this->info("Service created successfully: {$namespace}\\{$name}");
        return Command::SUCCESS;
    }
}
