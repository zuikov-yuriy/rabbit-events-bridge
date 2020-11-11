<?php

namespace TheP6\RabbitEventsBridge\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitevents-bridge:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all resources of rabbit-events-bridge';

    public function handle()
    {
        $this->comment('Publishing RabbitEventsBridfe Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'rabbitevents-bridge-provider']);

        $this->comment('Publishing RabbitEventsBridfe Routes example...');
        $this->callSilent('vendor:publish', ['--tag' => 'rabbitevents-bridge-resource']);

        $this->registerServiceProvider();

        $this->info('RabbitEventsBridge scaffolding installed successfully.');
    }

    protected function registerServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());
        $prefix = "{$namespace}\\Providers";
        $appConfig = file_get_contents($this->laravel->configPath('app.php'));
        if (Str::contains($appConfig, $prefix . 'RabbitEventsBridgeProvider::class')) {
            return;
        }
        file_put_contents(
            $this->laravel->configPath('app.php'),
            str_replace(
                "{$prefix}\\RabbitEventsServiceProvider::class," . PHP_EOL,
                "{$prefix}\\RabbitEventsServiceProvider::class," . PHP_EOL
                . "        {$prefix}\\RabbitEventsBridgeProvider::class," . PHP_EOL,
                $appConfig
            )
        );
        
        file_put_contents($this->laravel->path('Providers/RabbitEventsBridgeProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents($this->laravel->path('Providers/RabbitEventsBridgeProvider.php'))
        ));
    }
}
