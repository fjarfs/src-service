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
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Fjarfs\SrcService\Auth');
        $this->app->make('Fjarfs\SrcService\Exception');
        $this->app->make('Fjarfs\SrcService\Service');
        $this->app->make('Fjarfs\SrcService\Helpers\Security');
        $this->app->make('Fjarfs\SrcService\Middleware\AccessKey');
    }
}
