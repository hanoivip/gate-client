<?php

namespace Hanoivip\GateClient\Services;

use Hanoivip\Events\Gate\UserTopup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\GateClient\Models\StatisticType;
use Hanoivip\GateClient\Models\Statistic;

class UserTopupHandler
{
    // Only global statistic key is/can empty
    const GLOBAL_STAT_KEY = "";
    
    const STAT_CACHE_KEY = "cache_";
    
    const CACHE_INTERVAL = 3600;
    
    private $stat;
    
    public function __construct(StatisticService $stat)
    {
        $this->stat = $stat;
    }
    
    public function handle(UserTopup $event)
    {
        Log::debug("Statistics topup..");
    }
    
}