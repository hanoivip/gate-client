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
        return ['error' => 0, 'data' => ['balances' => $info]];
    }
    
    /**
     * action=add,remove
     * @param Request $request
     */
    public function modify(Request $request)
    {
        $action = $request->input('action');
        $userId = $request->input('uid');
        $type = $request->input('type');
        $value = $request->input('value');
        $reason = $request->input('reason');
        if (empty($type))
            $type = 0;
        
        switch ($action)
        {
            case 'add':
                return $this->balance->add($userId, $value, $reason, $type);
            case 'remove':
                return $this->balance->remove($userId, $value, $reason, $type);
            default:
                abort(500, 'Action not supported.');
        }
    }

}