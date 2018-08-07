<?php
namespace wouterNL\Drip;

use Illuminate\Support\ServiceProvider;

class DripServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/drip.php', 'drip'
        );

        $this->app->singleton('Drip', function() {
            return new DripPhp();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/drip.php' => config_path('drip.php'),
        ]);

    }

    public function provides()
    {
        return ['Drip'];
    }
}
