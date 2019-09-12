<?php

namespace Axit\LaravelArtisanVue;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class VueServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the package services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                VueCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            VueCommand::class,
        ];
    }
}
