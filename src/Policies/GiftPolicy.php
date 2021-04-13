<?php

namespace Hanoivip\GateClient\Policies;

use Hanoivip\GateClient\Contracts\ITopupPolicy;

// 10000,0.5;20000,0.5;30000,0.5;50000,1;100000,1;200000,1;300000,1;500000,1.5;1000000,1.5;2000000,1.5;3000000,1.5;5000000,1.5
// 10000,0.5;20000,0.5;30000,0.5;50000,0.5;100000,0.5;200000,1;300000,1;500000,1;1000000,1.5;2000000,1.5;3000000,1.5;5000000,1.5
class GiftPolicy implements ITopupPolicy
{
    protected $config;
    
    public function onTopup($type, $coin)
    {
        $params = $this->config->params;
        $rate = 0;
        if (!empty($params))
        {
            $rates = explode(';', $params);
            foreach ($rates as $r)
            {
                $valueRate = explode(',', $r);
                if ($coin == $valueRate[0])
                {
                    $rate = $valueRate[1];
                    break;
                }
            }
            
        }
        if (!empty($rate))
            return [ $type => $rate * $coin ];
        else
            return [];
    }

    public function info()
    {
        return $this->config;
    }

    public function setInfo($info)
    {
        $this->config = $info;
    }
}