<?php

namespace Hanoivip\GateClient\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\GateClient\Models\Submission;
use Hanoivip\GateClient\Services\BalanceService;
use Hanoivip\GateClient\Services\TopupService;
use Hanoivip\GateClient\Services\StatisticService;

/**
 * 
 * @author hanoivip
 *
 */
class AdminController extends Controller
{
    protected $topup;
    
    protected $balance;
    
    protected $stats;
    
    public function __construct(
        TopupService $topup, 
        BalanceService $balance, 
        StatisticService $stats)
    {
        $this->topup = $topup;
        $this->balance = $balance;
        $this->stats = $stats;
    }
    
    
    public function history(Request $request)
    {
        $tid = $request->input('tid');
        $submits = $this->topup->getHistory($tid);
        $mods = $this->balance->getHistory($tid);
        if ($request->ajax())
        {
            return ['error' => 0, 'message' => '', 'data' => ['submits' => $submits[0], 'mods' => $mods[0]]];
        }
        else 
        {
            return view('hanoivip::admin.topup-history', ['submits' => $submits[0], 'mods' => $mods[0]]);
        }
    }
    
}