<?php

namespace Hanoivip\GateClient\Services;

use Carbon\Carbon;
use Hanoivip\Events\Gate\UserTopup;
use Illuminate\Support\Facades\Log;
use Hanoivip\GateClient\Models\Policy;
use Hanoivip\GateClient\Policies\GiftPolicy;
use Hanoivip\GateClient\Policies\GateCardSale;
use Hanoivip\GateClient\Policies\ZingCardSale;
use Hanoivip\GateClient\Policies\MomoSale;

class PolicyService
{
    protected $balance;
    
    public function __construct(BalanceService $balance)
    {
        $this->balance = $balance;    
    }
    
    public function getSystemPolicies()
    {
        $now = time();
        $policies = Policy::where('target_uid', 0)
        ->where('end_time', '>', $now)
        ->get();
        $all = [];
        foreach ($policies as $pol)
        {
            $polObj = $this->getPolicyImpl($pol->type);
            $polObj->setInfo($pol);
            $all[] = $polObj;
        }
        return $all;
    }
    
    public function getUserPolicies($uid)
    {
        $now = time();
        $policies = Policy::where('start_time', '<=', $now)
                    ->where('end_time', '>', $now)
                    ->get();
        $all = [];
        foreach ($policies as $pol)
        {
            $polObj = $this->getPolicyImpl($pol->type);
            $polObj->setInfo($pol);
            $all[] = $polObj;
        }
        return $all;
    }
    
    private function getPolicyImpl($type) 
    {
        switch ($type)
        {
            case 0: return new GiftPolicy();
            case 1: return new GateCardSale();
            case 2: return new ZingCardSale();
            case 3: return new MomoSale();
        }
    }
    
    public function handle(UserTopup $event)
    {
        $myPolicies = $this->getUserPolicies($event->uid);
        /** @var ITopupPolicy $pol */
        foreach ($myPolicies as $pol)
        {
            $gift = $pol->onTopup($event->type, $event->coin);
            if (!empty($gift))
            {
                Log::debug('Topup policy ' . $pol->info()->title . ' applied. New gift: ' . print_r($gift, true));
                foreach ($gift as $type => $value)
                {
                    if ($value > 0)
                    {
                        $this->balance->add($event->uid, $value,
                            'TopupPolicy:' . $pol->info()->title . ':' . $event->mapping, $type);
                    }
                    else
                    {
                        $this->balance->remove($event->uid, -1 * $value,
                            'TopupPolicy:' . $pol->info()->title . ':' . $event->mapping, $type);
                    }
                }
            }
        }
    }
    
    /**
     * 
     * @param number $type
     * @param string $title
     * @param string $start
     * @param string $end
     * @return true|string 
     */
    public function newSystemPolicy($type, $title, $start, $end, $params = null)
    {
        $startTs = Carbon::parse($start)->timestamp;
        $endTs = Carbon::parse($end)->timestamp;
        $set1 = Policy::where('target_uid', 0)->where('type', $type)
        ->where('start_time', '>=', $startTs)
        ->where('start_time', '<', $endTs)->get();
        if ($set1->isNotEmpty())
            return 1;
        $set2 = Policy::where('target_uid', 0)->where('type', $type)
        ->where('end_time', '>', $startTs)
        ->where('end_time', '<=', $endTs)->get();
        if ($set2->isNotEmpty())
            return 2;
        $policy = new Policy();
        $policy->type = $type;
        $policy->title = $title;
        $policy->start_time = $startTs;
        $policy->end_time = $endTs;
        $policy->params = $params;
        $policy->save();
        return true;
    }
    
    public function delete($pid)
    {
        $pol = Policy::where('id', $pid)->get();
        if ($pol->isEmpty())
            return 1;
        $pol->first()->delete();
        return true;
    }
}