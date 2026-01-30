<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class MakeEnumCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Enum class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $path = app_path("Enums/{$name}.php");

        if (! is_dir(app_path('Enums'))) {
            mkdir(app_path('Enums'));
        }

        if (file_exists($path)) {
            $this->error('Enum already exists!');
            return self::FAILURE;
        }

        $stub = <<<PHP
<?php

namespace App\Enums;

enum {$name}: string
{
    //
}
PHP;

        file_put_contents($path, $stub);

        $this->info("Enum {$name} created successfully.");
        return self::SUCCESS;
    }
}
