<?php

namespace Hanoivip\GateClient\Services;

use Hanoivip\GateClient\Models\BalanceMod;
use Hanoivip\GateClient\Models\Balance;
use Illuminate\Support\Facades\Log;
use Hanoivip\GateClient\Contracts\IBalance;
use Hanoivip\Events\Gate\UserFirstTopup;

class BalanceService implements IBalance
{
    /**
     * Truy xuất tất cả các loại tài khoản của ng chơi.
     * 
     * @param number $uid
     * @return Balance
     */
    public function getInfo($uid)
    {
        $balances = Balance::where('user_id', $uid)->get();
        return $balances;
    }
    
    /**
     * 
     * @param number $uid
     * @param string $type
     * @param number $value
     * @param string $reason
     * @return boolean
     */
    public function add($uid, $value, $reason, $type = 0)
    {
        if ($value <= 0)
        {
            Log::warn("Balance value is zero or negative. skip!");
            return;
        }
        $balance = Balance::where('user_id', $uid)
                        ->where('balance_type', $type)
                        ->first();
        if (empty($balance))
        {
            $balance = new Balance();
            $balance->user_id = $uid;
            $balance->balance_type = $type;
            $balance->balance = $value;
            $balance->save();
            
            if ($type == 0)
                event(new UserFirstTopup($uid, $value));
        }
        else
        {
            $balance->balance += $value;
            $balance->save();
        }
        
        $log = new BalanceMod();
        $log->user_id = $uid;
        $log->balance_type = $type;
        $log->balance = $value;
        $log->reason = $reason;
        $log->save();
        
        return true;
    }
    
    /**
     * 
     * @param number $uid
     * @param string $type Value to substract. Positive value.
     * @param number $value
     * @param string $reason
     * @return boolean
     */
    public function remove($uid, $value, $reason, $type = 0)
    {
        if ($value <= 0)
        {
            Log::warn("Balance value is zero or negative. skip!");
            return false;
        }
        $balance = Balance::where('user_id', $uid)
            ->where('balance_type', $type)
            ->first();
        if (empty($balance))
        {
            Log::debug("Balance user {$uid} has not balance type {$type} yet.");
            return false;
        }
        if ($balance->balance < $value)
        {
            Log::debug("Balance user {$uid} has not enough balance");
            return false;
        }
        $balance->balance -= $value;
        $balance->save();
        
        $log = new BalanceMod();
        $log->user_id = $uid;
        $log->balance_type = $type;
        $log->balance = -1 * $value;
        $log->reason = $reason;
        $log->save();
        
        return true;
    }
    
    /**
     * 
     * @param number $uid User ID
     * @param number $page Requested Page
     * @param number $count Number of rows to fetch
     * @return \stdClass[]
     */
    public function getHistory($uid, $page = 1, $count = 10)
    {
        $mods = BalanceMod::where('user_id', $uid)
        ->skip(($page - 1) * $count)
        ->take($count)
        ->orderBy('id', 'desc')
        ->get();
        $objects = [];
        foreach ($mods as $mod)
        {
            $obj = new \stdClass();
            $obj->balance = $mod->balance;
            $obj->acc_type = $mod->balance_type == 0 ;//? 'TK chính' : 'TK phụ';
            $list = explode(':', $mod->reason);
            if($list[0]=='Recharge'){
                $reason = 'Chuyển Vào' . $list[3];
            }elseif ($list[0]=='TopupPolicy') {
                $reason = 'Khuyến Mãi';
            }elseif ($list[0]=='Topup') {
                $reason = 'Nạp Thẻ';
            } elseif ($list[0] == 'Proceed') {
                $reason = 'Xúc tiến';    
            }else{
                $reason=$list[0];
            }
            $obj->reason = $reason;
            $obj->time = $mod->created_at;//Carbon::parse($mod->created_at)->format('d/M/Y m:H');
            $objects[] = $obj;
        }
        $total = BalanceMod::where('user_id', $uid)->count();
        return [$objects, ceil($total / 10), $page];
    }
    
    public function enough($uid, $coin, $type = 0)
    {
        $balance = Balance::where('user_id', $uid)
                        ->where('balance_type', $type)
                        ->first();
        if (!empty($balance))
            return $balance->balance >= $coin;
        return false;
    }


}