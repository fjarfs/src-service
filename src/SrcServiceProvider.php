<?php

namespace Fjarfs\SrcService;

use Illuminate\Support\ServiceProvider;

class SrcServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/srcservice.php' => config_path('srcservice.php'),
            __DIR__ . '/../config/applocale.php' => config_path('applocale.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/srcservice.php', 'srcservice');
        $this->mergeConfigFrom(__DIR__ . '/../config/applocale.php', 'applocale');

        $this->app->make('Fjarfs\SrcService\Auth');
        $this->app->make('Fjarfs\SrcService\Exception');
        $this->app->make('Fjarfs\SrcService\Service');
        $this->app->make('Fjarfs\SrcService\Helpers\Security');
        $this->app->make('Fjarfs\SrcService\Middleware\AccessKey');
    }
}
