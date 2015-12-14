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
        $this->app->singleton('crud', function($app) {
            return new Crud($app['translator'], $app['view']);
        });

        $this->app['command.crud.generate'] = $this->app->share(function() {
            return new Console\GenerateCommand;
        });

        $this->commands([
            'command.crud.generate'
        ]);

        $this->app->alias('crud', 'Axn\CrudGenerator\Crud');
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
            __DIR__.'/../resources/views/' => base_path('resources/views/vendor/crud-generator'),
            __DIR__.'/../resources/lang/fr/' => base_path('resources/lang/packages/fr/crud-generator'),
            __DIR__.'/../resources/stubs/' => base_path('resources/stubs/vendor/crud-generator'),
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'crud',
            'command.crud.generate'
        ];
    }
}
