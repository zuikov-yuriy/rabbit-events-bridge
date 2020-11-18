<?php

namespace TheP6\RabbitEventsBridge\Providers;

use Illuminate\Support\ServiceProvider;
use TheP6\RabbitEventsBridge\Console\InstallCommand;
use TheP6\RabbitEventsBridge\MessageRouter\MessageRouter;

class RabbitEventsBridgeProvider extends ServiceProvider
{
    protected string $baseRoutingFile = 'routes-rabbit-events-bridge/routes.php';

    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class
        ]);

        $this->loadRouting();
    }

    public function register()
    {
        $this->offerPublishing();

        $this->app->singleton(MessageRouter::class);
    }

    protected function loadRouting()
    {
        if (file_exists($this->app->basePath($this->baseRoutingFile))) {
            require $this->app->basePath($this->baseRoutingFile);
        }
    }

    /**
     * Setup the resource publishing groups for RabbitEvents.
     *
     * @return void
     */
    protected function offerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $providerName = 'RabbitEventsBridgeProvider';

            $this->publishes([
                __DIR__ . "/../../stubs/{$providerName}.stub" => $this->app->path("Providers/{$providerName}.php"),
            ], 'rabbitevents-bridge-provider');
            $this->publishes([
                __DIR__ . '/../../routes-rabbit-events-bridge/routes.php' => $this->app->basePath('routes-rabbit-events-bridge/routes.php'),
            ], 'rabbitevents-bridge-resource');
        }
    }
}