<?php

namespace Gabrieldev\ConsentMode;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ConsentModeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/consent-mode.php', 'consent-mode');
        $this->app->bind(ConsentState::class, fn ($app) => new ConsentState($app['config']['consent-mode']));
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'consent-mode');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'consent-mode');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'consent-mode');

        $this->publishes([__DIR__.'/../config/consent-mode.php' => config_path('consent-mode.php')], 'consent-mode-config');
        $this->publishes([__DIR__.'/../resources/views' => resource_path('views/vendor/consent-mode')], 'consent-mode-views');
        $this->publishes([__DIR__.'/../lang' => $this->app->langPath('vendor/consent-mode')], 'consent-mode-lang');
    }
}
