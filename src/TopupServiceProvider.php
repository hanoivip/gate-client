<?php

namespace Hanoivip\GateClient;

use Illuminate\Support\ServiceProvider;
use Hanoivip\GateClient\Contracts\IGateClient;
use Hanoivip\GateClient\Services\RemoteGateClient;
use Hanoivip\GateClient\Services\GateResponseParser;
use Hanoivip\GateClient\Services\TestGateResponseParser;
use Hanoivip\GateClient\Services\BalanceService;
use Hanoivip\GateClient\Policies\GiftPolicy;

class TopupServiceProvider extends ServiceProvider
{
    public function register()
    {
        /*
        if ($this->app->environment('local')) {
            $this->app->bind("GateResponseParser", function ($app, $text) {
                return new TestGateResponseParser($text);
            });
        }
        if ($this->app->environment('production')) {
            $this->app->bind("GateResponseParser", function ($app, $text) {
                return new GateResponseParser($text);
            });
        }
        $this->app->bind(IGateClient::class, RemoteGateClient::class);
        */
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