<?php

namespace Hanoivip\GateClient\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\GateClient\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    protected $balance;
    
    public function __construct(BalanceService $balance)
    {
        $this->balance = $balance;
    }
    
    public function info(Request $request)
    {
        $uid = Auth::user()->getAuthIdentifier();
        $balances = $this->balance->getInfo($uid);
        $info = [];
        foreach ($balances as $bal)
        {
            $info[] = ['type' => $bal->balance_type, 'balance' => $bal->balance];
        }
        return ['error' => 0, 'message' => 'success', 'data' => ['balances' => $info]];
    }

}