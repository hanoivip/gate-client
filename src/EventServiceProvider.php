<?php

namespace Hanoivip\GateClient;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Hanoivip\Events\Gate\UserTopup' => [
            'Hanoivip\GateClient\Services\PolicyService',
            'Hanoivip\GateClient\Activities\RankingActivity',
            'Hanoivip\GateClient\Services\UserTopupHandler',
        ],
        'Hanoivip\GateClientNew\Event\DelayCard' => [
            'Hanoivip\GateClient\Services\TopupService',
        ]
    ];
    
    public function boot()
    {
        parent::boot();
    }
}