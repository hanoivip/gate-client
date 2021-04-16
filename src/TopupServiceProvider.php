<?php

namespace Hanoivip\GateClient;

use Hanoivip\GateClient\Policies\GiftPolicy;
use Hanoivip\GateClient\Services\BalanceService;
use Illuminate\Support\ServiceProvider;

class TopupServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind("BalanceService", BalanceService::class);
        $this->app->bind("GiftPolicy", function ($app, $cfg) {
            return new GiftPolicy($cfg);
        });
        $this->commands([
            \Hanoivip\GateClient\Commands\PolicyNew::class,
        ]);
    }
    
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/payment.php' => config_path('payment.php'),
            __DIR__ . '/../resources/assets' => resource_path('assets/vendor/hanoivip'),
            __DIR__ . '/../resources/images' => public_path('img'),
            __DIR__.'/../lang' => resource_path('lang/vendor/hanoivip'),
        ]);
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../views', 'hanoivip');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadTranslationsFrom( __DIR__.'/../lang', 'hanoivip');
    }
}