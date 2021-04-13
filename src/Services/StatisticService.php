<?php

namespace Hanoivip\GateClient\Services;

use Hanoivip\Events\Gate\UserTopup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\GateClient\Models\StatisticType;
use Hanoivip\GateClient\Models\Statistic;

class StatisticService
{
    // Only global statistic key is/can empty
    const GLOBAL_STAT_KEY = "";
    
    const STAT_CACHE_KEY = "cache_";
    
    const CACHE_INTERVAL = 3600;
    
    public function handle(UserTopup $event)
    {
        Log::debug("Statistics topup..");
        $now = time();
        $types = StatisticType::where('start_time', '<=', $now)
                        ->where('end_time', '>', $now)
                        ->where('disable', false)
                        ->get();
        
        // add default                
        $globalStat = $this->getGlobalStatType();
        $types->push($globalStat);
        
        /** @var StatisticType $type */
        foreach ($types->all() as $type)
        {
            $this->stat($type->key, $event->uid, $event->coin);
        }
    }
    
    /**
     * Add or create new record
     * 
     * @param string $key
     * @param integer $uid
     * @param integer $coin
     */
    private function stat($key, $uid, $coin)
    {
        $stat = Statistic::where('key', $key)
                        ->where('user_id', $uid)
                        ->get();
        if ($stat->count() > 1)
        {
            Log::error('Statistic more than 2 record with single key (per user)');
        }
        else if ($stat->count() <= 0)
        {
            $newStat = new Statistic();
            $newStat->key = $key;
            $newStat->user_id = $uid;
            $newStat->total = $coin;
            $newStat->save();
        }
        else
        {
            // add
            $curStat = $stat->first();
            $curStat->total += $coin;
            $curStat->save();
        }
            
    }
    
    private function getGlobalStatType()
    {
        $stat = new StatisticType();
        $stat->key = self::GLOBAL_STAT_KEY;
        $stat->start_time = 0;
        $stat->end_time = 0;
        return $stat;
    }
    
    public function getGlobalStatistics($count = 10)
    {
        return $this->getStatistics(self::GLOBAL_STAT_KEY, $count);
    }
    
    public function getStatistics($key, $count = 10)
    {
        $cacheKey = self::STAT_CACHE_KEY . $key;
        if (Cache::has($cacheKey))
            return Cache::get($cacheKey);
        if ($count > 0)
            $stats = Statistic::where('key', $key)
            ->orderBy('total', 'desc')
            ->limit($count)
            ->get();
        else
            $stats = Statistic::where('key', $key)
            ->orderBy('total', 'desc')
            ->get();
        
        $expires = now()->addSeconds(self::CACHE_INTERVAL);
        Cache::put($cacheKey, $stats->all(), $expires);
        return $stats->all();
    }
    
    public function addKey($key, $starttime = 0, $endtime = 0)
    {
        if ($key == self::GLOBAL_STAT_KEY)
            throw new Exception("Statistic can not add new key. The key value is reserved.");
        $stat = StatisticType::where('key', $key)
            ->get();
        if ($stat->isNotEmpty())
        {
            Log::error("Statistic key is duplicated.");
            return false;
        }
        if (empty($starttime))
            $starttime = time();
        if (empty($endtime))
            $endtime = time() + 10 * 365 * 86400;
        
        $stat = new StatisticType();
        $stat->key = $key;
        $stat->start_time = $starttime;
        $stat->end_time = $endtime;
        $stat->save();
        return true;    
    }
    
    public function removeKey($key)
    {
        $stat = StatisticType::where('key', $key)
                        ->get();
        if ($stat->isNotEmpty())
        {
            foreach ($stat->all() as $type)
            {
                $type->disable = true;
                $type->save();
            }
        }
    }
    
}