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
class TopupController extends Controller
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
    
    public function topupHistory(Request $request)
    {
        $page = 1;
        if ($request->has('page'))
            $page = $request->input('page');
        $uid = Auth::user()->getAuthIdentifier();
        $history = $this->topup->getHistory($uid, $page);
        if ($request->ajax())
        {
            return ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]];
        }
        else
        {
            return view('hanoivip::topup-history', ['submits' => $history[0], 'total_page' => $history[1], 'current_page' => $history[2]]);
        }
    }
    
    public function rechargeHistory(Request $request)
    {
        $page = 1;
        if ($request->has('page'))
            $page = $request->input('page');
        $uid = Auth::user()->getAuthIdentifier();
        $mods = $this->balance->getHistory($uid, $page);
        if ($request->ajax())
        {
            return ['mods' => $mods[0], 'total_page' => $mods[1], 'current_page' => $mods[2]];
        }
        else
        {
            return view('hanoivip::recharge-history', ['mods' => $mods]);
        }
    }
    
    public function history(Request $request)
    {
        $uid = Auth::user()->getAuthIdentifier();
        $submits = $this->topup->getHistory($uid);
        $mods = $this->balance->getHistory($uid);
        if ($request->ajax())
        {
            return ['submits' => $submits[0], 'mods' => $mods[0]];
        }
        else 
        {
            return view('hanoivip::history', ['submits' => $submits[0], 'mods' => $mods[0]]);
        }
    }
    
    public function globalRank()
    {
        return $this->stats->getGlobalStatistics();
    }
    
    public function rank($key)
    {
        return $this->stats->getStatistics($key, 0);
    }
    
    public function topupUI2(Request $request)
    {
        $uid = Auth::user()->getAuthIdentifier();
        $cardtypes = $this->topup->getGateStatus();
        $enabled = $this->topup->getEnableTypes($uid);
        $cutoffs = $this->topup->getCutoffs($uid);
        if ($request->ajax())
        {
            return ['cardtypes' => $cardtypes, 'enabled' => $enabled, 'cutoffs' => $cutoffs];
        }
        else 
        {
            return view('hanoivip::topup-select-type', 
                ['cardtypes' => $cardtypes, 'enabled' => $enabled, 'cutoffs' => $cutoffs]);
        }
    }
    
    public function selectType(Request $request)
    {
        $type = $request->input('type');
        $dvalue = $request->input('dvalue');
        try 
        {
            $uid = Auth::user()->getAuthIdentifier();
            $result = $this->topup->prepareByType($uid, $type, $dvalue);
            if ($request->ajax())
            {
                return ['result' => $result];
            }
            else 
            {
                if (gettype($result) == 'string')
                    return view('hanoivip::topup_result', ['error_message' => $result]);
                else
                    return view('hanoivip::topup-input', ['params' => $result]);
            }
        }
        catch (Exception $ex)
        {
            Log::error('Topup prepare payment exception. Msg:' . $ex->getMessage());
            return view('hanoivip::topup_result', 
                ['error_message' => __('hanoivip::topup.system-error')]);
        }
    }
    
    public function topup2(Request $request)
    {
        try 
        {
            $uid = Auth::user()->getAuthIdentifier();
            $submission = $this->topup->prerouted($uid, $request->all());
            if (gettype($submission) == 'string')
            {
                if ($request->ajax())
                    return ['result' => ['delay' => false, 'mapping' =>'', 'error_message' => $submission]];
                else
                    return view('hanoivip::topup_result', [ 'error_message' => $submission]);
            }
            else
            {
                if ($submission->success)
                {
                    $message = $submission->message;
                }
                else
                {
                    $error_message = $submission->message;
                }
                if ($request->ajax())
                {
                    $result = ['delay' => false, 'mapping' => $submission->mapping];
                    if (isset($message)) {
                        $result['message'] = $message;
                        // add success page for tracking
                        $result['topath'] = route('topup.success', ['message' => $message]);
                    }
                    if (isset($error_message))
                        $result['error_message'] = $error_message;
                    if ($submission->delay)
                        $result['delay'] = true;
                    return ['result' => $result];
                }
                else
                {
                    if (isset($message))
                        return view('hanoivip::topup_result', [ 'message' => $message]);
                    if (isset($error_message))
                        return view('hanoivip::topup_result', [ 'error_message' => $error_message]);
                }
            }
        }
        catch (Exception $ex)
        {
            Log::error('Topup payment exception. Msg:' . $ex->getMessage());
            if ($request->ajax())
                return ['result' => ['error_message' => __('hanoivip::topup.system-error')]];
            return view('hanoivip::topup_result', 
                [ 'error_message' => __('hanoivip::topup.system-error') ]);
        }
    }
    
    public function recaptcha(Request $request)
    {
        $uid = Auth::user()->getAuthIdentifier();
        try
        {
            $result = $this->topup->recaptcha($uid);
            if (gettype($result) == 'string')
                return view('hanoivip::topup_result', ['error_message' => $result]);
            else
                return view('hanoivip::topup-input', ['params' => $result]);
        }
        catch (Exception $ex)
        {
            Log::error('Topup prepare payment exception. Msg:' . $ex->getMessage());
            return view('hanoivip::topup_result',
                [ 'error_message' => __('hanoivip::topup.system-error') ]);
        }
    }
    
    public function cancel(Request $request)
    {
        try
        {
            $uid = Auth::user()->getAuthIdentifier();
            $this->topup->cancel($uid);
        }
        catch (Exception $ex)
        {
            Log::error('Topup cancel exception. Msg:' . $ex->getMessage());
        }
        return redirect()->route('topup');
    }
    
    public function query(Request $request)
    {
        try
        {
            $mapping = $request->input('mapping');
            $submission = Submission::where('mapping', $mapping)->first();
            if (!empty($submission))
            {
                $result = ['success' => $submission->success, 'delay' => $submission->delay, 'value' => $submission->value];
                return ['result' => $result];
            }
        }
        catch (Exception $ex)
        {
            Log::error("Query transaction error: " . $ex->getMessage());
            abort(500);
        }
    }
    
    public function getRule(Request $request)
    {
        try 
        {
            if ($request->ajax())
                return ['html' => __('hanoivip::topup.rule')];
            else
                return view('topup-rule', ['html' =>  __('hanoivip::topup.rule')]);
        } catch (Exception $ex) {
            Log::error("Get topup rule error: " . $ex->getMessage());
            abort(500);
        }
    }
    
    public function getLang(Request $request)
    {
        try
        {
            if ($request->ajax())
            {
                return ['data' => __('hanoivip::topup')];
            }
            abort(404);
        }
        catch (Exception $ex)
        {
            Log::error("Topup get lang error: " . $ex->getMessage());
            abort(500);
        }
    }
    
    public function jsTopup(Request $request)
    {
        $lang = __('hanoivip::topup');
        return view('hanoivip::jtopup', ['lang' => json_encode($lang)]);
    }
    
    public function jsHistory(Request $request)
    {
        $lang = __('hanoivip::topup');
        return view('hanoivip::jhistory', ['lang' => json_encode($lang)]);
    }
    
    public function jsRecharge(Request $request)
    {
        return view('hanoivip::jrecharge');
    }
    
    public function onTopupSuccess(Request $request)
    {
        $message = 'Topup Success!';
        if ($request->has('message'))
            $message = $request->input('message');
        return view('hanoivip::topup-success', ['message' => $message]);
    }
}