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
        $this->publishes([
            __DIR__.'/../resources/stubs/' => base_path('resources/stubs/vendor/crud-generator'),
        ], 'crud-generator.stubs');
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
