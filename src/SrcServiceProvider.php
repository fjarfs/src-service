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
    }
}
