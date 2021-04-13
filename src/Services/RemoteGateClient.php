<?php

namespace Hanoivip\GateClient\Services;

use Hanoivip\GateClient\Contracts\IGateClient;
use Illuminate\Support\Facades\Log;

//TODO: make gate-client module with Facade
//Rename this to payment
class RemoteGateClient implements IGateClient
{
    public function check($type, $serial, $password, $mapping)
    {
        $url = config('gate.uri') . "/api/topup/client/" . config('gate.partner') .
                '/serial/' . $serial . '/password/' . $password . '/type/' . $type . '/mapping/' . $mapping;
        $response = \CurlHelper::factory($url)->exec();
        return $response['content'];
    }

    public function status()
    {
        $url = config('gate.uri') . "/api/status";
        Log::debug('GateProxy dump status url:' . $url);
        $response = \CurlHelper::factory($url)->exec();
        Log::debug('GateProxy dump status:' . $response['content']);
        return $response['data'];
    }
    
    public function prepare($type, $dvalue)
    {
        $data = ['client' => config('gate.partner'), 'type' => $type, 'dvalue' => $dvalue];
        $url = config('gate.uri') . "/api/" . config('gate.version') . "/topup/prepare?" . http_build_query($data);
        Log::debug('GateProxy dump prepare url:' . $url);
        $response = \CurlHelper::factory($url)->exec();
        Log::debug('GateProxy dump prepare response:' . print_r($response['data'], true));
        return $response['data'];
    }

    public function checkPrerouted($type, $dvalue, $serial, $password, 
                                    $mapping, $pid, $sid = null, $captcha = null)
    {
        $data = ['client' => config('gate.partner'),  
            'type' => $type, 'dvalue' => $dvalue, 
            'serial' => $serial, 'password' => $password, 
            'mapping' => $mapping, 'captcha' => $captcha,
            'pid' => $pid, 'sid' => $sid
        ];
        $url = config('gate.uri') . "/api/" . config('gate.version') . "/topup?" . http_build_query($data);
        $response = \CurlHelper::factory($url)->exec();
        return $response['content'];
    }

    public function recaptcha($type, $pid, $sid, $dvalue)
    {
        $data = ['type' => $type, 'pid' => $pid, 'sid' => $sid, 'dvalue' => $dvalue];
        $url = config('gate.uri') . "/api/" . config('gate.version') . "/topup/recaptcha?" . http_build_query($data);
        $response = \CurlHelper::factory($url)->exec();
        return $response['data'];
    }
    
    public function cancel($pid, $sid)
    {
        $data = ['pid' => $pid, 'sid' => $sid];
        $url = config('gate.uri') . "/api/" . config('gate.version') . "/topup/cancel?" . http_build_query($data);
        $response = \CurlHelper::factory($url)->exec();
        return true;
    }
}