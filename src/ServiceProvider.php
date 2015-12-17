<?php

namespace Axn\CrudGenerator;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['command.crud.generate'] = $this->app->share(function() {
            return new Console\GenerateCommand;
        });

        $this->commands([
            'command.crud.generate'
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'crud-generator');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'crud-generator');

        $this->publishes([
            __DIR__.'/../resources/views/' => base_path('resources/views/vendor/crud-generator')
        ], 'views');

        if (version_compare($this->app->version(), '5.1') < 0) {
            // Compatibilité Laravel 5.0 pour la publication des traductions
            $this->publishes([
                __DIR__.'/../resources/lang/fr/' => base_path('resources/lang/packages/fr/crud-generator')
            ], 'lang');
        } else {
            $this->publishes([
                __DIR__.'/../resources/lang/' => base_path('resources/lang/vendor/crud-generator')
            ], 'lang');
        }

        $this->publishes([
            __DIR__.'/../resources/stubs/' => base_path('resources/stubs/vendor/crud-generator'),
        ], 'stubs');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.crud.generate'
        ];
    }
}
