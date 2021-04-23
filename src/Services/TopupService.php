<?php

namespace Hanoivip\GateClient\Services;

use Carbon\Carbon;
use Hanoivip\GateClientNew\Event\DelayCard;
use Hanoivip\GateClientNew\Facade\GateFacade;
use Hanoivip\GateClient\Models\Submission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Events\Gate\UserTopup;
use stdClass;

class TopupService
{   
    protected $balance;
    
    public function __construct(BalanceService $balance)
    {
        $this->balance = $balance;
    }
    
    /**
     * Lấy về trạng thái của cổng nạp thẻ
     * - Có thể tự cache
     * 
     */
    public function getGateStatus()
    {
        $key = "GateStatus";
        if (Cache::has($key))
            return Cache::get($key);
        try 
        {
            $status = GateFacade::status();
            Cache::put($key, $status, Carbon::now()->addMinutes(5));
            return $status;
        } 
        catch (Exception $e) 
        {
            Log::error("TopupService check gate status error: " . $e->getMessage());
        }   
    }
    /**
     * Lấy các thẻ người chơi đc nạp
     * @param number $uid
     * @return array
     */
    public function getEnableTypes($uid)
    {
        $enabled = config('payment.enabled_types');
        if (empty($enabled))
            Log::warn('Gate enabled types is empty');
        return $enabled;
    }
    /**
     * Lấy chiết khấu tương ứng của ng chơi
     * @param number $uid
     * @return array
     */
    public function getCutoffs($uid)
    {
        $cutoffs = config('payment.cutoffs');
        return $cutoffs;
    }
    
    /**
     * Lấy thời gian nạp lỗi gần nhất
     * 
     * @param number $uid
     * @return number
     */
    private function getLastErrorTs($uid)
    {
        $key = 'LastErrorTs:' . $uid;
        if (Cache::has($key))
        {
            return Cache::get($key);
        }
        return 0;
    }
    
    private function setLastErrorTs($uid)
    {
        $key = 'LastErrorTs:' . $uid;
        $expires = Carbon::now()->addMinutes(5);
        if (Cache::has($key))
            Cache::put($key, time(), $expires);
        else
            Cache::add($key, time(), $expires);
    }
    /**
     * 
     * @param number $uid UserID
     * @param number $page Request page..
     * @param number $count Count to get
     * @return array 0: records, 1: total of page, 2: current page
     */
    public function getHistory($uid, $page = 1, $count = 10)
    {
        $submissions = Submission::where('user_id', $uid)
        ->skip($count * ($page - 1))
        ->take($count)
        ->orderBy('id', 'desc')
        ->get();
        $objects = [];
        foreach ($submissions as $sub)
        {
            $obj = new \stdClass();// fast, no need to create view objects class...
            //$obj->serial = $sub->serial;
            $obj->password = $sub->password;
            //$obj->message = $this->getExplainMessage($sub);
            $obj->status = $this->getSubmissionStatus($sub);
            $obj->dvalue = $sub->dvalue;
            $obj->value = $sub->value;
            $obj->penalty = $sub->penalty;
            //$obj->final_value = $sub->final_value;
            $obj->mapping = $sub->mapping;
            $obj->delay = $sub->delay;
            $obj->success = $sub->success;
            $obj->time = $sub->created_at;//Carbon::parse($sub->created_at)->format('d/M/Y m:H');
            $objects[] = $obj;
        }
        $total = Submission::where('user_id', $uid)->count();
        return [$objects, ceil($total / 10), $page];
    }
    
    public function getSubmissionStatus($submission)
    {
        $status = 0;
        if ($submission->success)
        {
            $value = $submission->value;
            if ($submission->delay && empty($value))
                $status = 2;
            else if ($value != $submission->dvalue)
                $status = 3;
        }
        else
        {
            $status = 1;
        }
        return $status;
    }
    
    public function getExplainMessage($submission)
    {
        $parser = app()->makeWith('GateResponseParser', [ $submission->api_returned ]);
        if ($parser->isSuccess())
        {
            $value = $submission->value;
            if ($parser->isDelay() && empty($value))
                $message = __('hanoivip::topup.delay');
            else
            {
                $message = __('hanoivip::topup.success', ['value' => $value]);
                if ($submission->dvalue > 0 && $submission->dvalue != $submission->value)
                    $message .= __('hanoivip::topup.success-wrong-value', ['cutoff' => $this->getWrongValueCutoff()]);
            }
        }
        else
        {
            $message = $parser->getExplainMessage();
        }
        return $message;
    }
    
    
    /**
     * Trả về thẻ trễ. Nếu thẻ đúng cập nhật giá trị.
     * Nếu thẻ sai, cập nhật trạng thái
     * 
     * @param string $mapping
     * @param number $value
     * @param boolean $turnFailed
     * @return boolean
     */
    public function callback($mapping, $value, $turnFailed)
    {
        $submission = Submission::where('mapping', $mapping)->first();
        if (!empty($submission))
        {
            if ($submission->value == 0 && boolval($submission->delay))
            {
                if ($value > 0)
                {
                    Log::debug('Topup callback to update transation value.');
                    $submission->value = $value;
                    $finalValue = $value;
                    if ($submission->dvalue > 0 && $submission->dvalue != $value)
                    {
                        $finalValue = intval(min($submission->dvalue, $value) * (100 - $this->getWrongValueCutoff()) / 100);
                        $submission->penalty = $this->getWrongValueCutoff();
                    }
                    $submission->final_value = $finalValue;
                    $submission->save();
                    $this->balance->add($submission->user_id, $finalValue, "Topup:" . $mapping);
                    event(new UserTopup($submission->user_id, 0, $finalValue, $mapping));
                }
                else if ($turnFailed)
                {
                    Log::debug('Topup callback to update transation from delay to fail.');
                    $submission->success = false;
                    $submission->message = 'Thẻ trễ, sau kiểm tra thấy sai!';
                    $submission->save();
                }
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param number $uid User ID
     * @param string $type User choosen card type
     * @param number $dvalue User declared card value
     * @return array|string Param array need for payment, String message if fail
     */
    public function prepareByType($uid, $type, $dvalue)
    {
        $route = GateFacade::routing($type, $dvalue);
        /** @var IRoutingResult $route */
        if (!$route->isAvaiable())
            return __('hanoivip::topup.channel-maintain');
        if ($route->isBusy())
            return __('hanoivip::topup.channel-unavailable');
        // Save temp data
        $oldRoute = $this->getTopupSession($uid);
        if (!empty($oldRoute))
        {
            // Refreshed, not cancel properly
            $this->cancel($uid);
        }
        $this->saveTopupSession($uid, $route->toArray());
        return $route->toArray();
    }
    
    protected function clearTopupSession($uid)
    {
        $key = "TopupSession" . $uid;
        if (Cache::has($key))
            Cache::forget($key);
    }
    
    protected function getTopupSession($uid)
    {
        $key = "TopupSession" . $uid;
        if (Cache::has($key))
            return Cache::get($key);
    }
    
    protected function saveTopupSession($uid, $route)
    {
        $key = "TopupSession" . $uid;
        $expires = Carbon::now()->addMinutes(5);
        Cache::add($key, $route, $expires);
    }
    
    public function prerouted($uid, $params)
    {
        $route = $this->getTopupSession($uid);
        if (empty($route))
            return __('hanoivip::topup.timeout');
        $session = $route['session'];
        $type = $route['type'];
        $dvalue = $route['dvalue'];
        $serial = $params['serial'];
        $password = $params['password'];
        $captcha = isset($params['captcha']) ? $params['captcha'] : '';
        
        // Limit failure rate. TODO: move to throtte
        $lastErrorTs = $this->getLastErrorTs($uid);
        if (time() - $lastErrorTs < 60)
            return 'Lần nạp thẻ trước bị sai. Cần đợi 1p trước khi tiếp tục. Còn (' . (60 - time() + $lastErrorTs) . ' giây)';
        // New submission
        $mapping = uniqid();
        $sub = new Submission();
        $sub->user_id = $uid;
        $sub->serial = $serial;
        $sub->password = $password;
        $sub->type = $type;
        $sub->dvalue = $dvalue;
        $sub->penalty = 0;
        $sub->mapping = $mapping;
        // Invoke
        $card = new stdClass();
        $card->serial = $serial;
        $card->password = $password;
        $card->captcha = $captcha;
        $result = GateFacade::prepaid($session, $card, $mapping);
        /** @var \Hanoivip\GateClientNew\ITopupResult $result */
        $sub->success = $result->isSuccess();
        $sub->delay = $result->isDelay();
        $sub->message = $result->getExplainMessage();
        
        // Return
        if ($result->getValue() > 0)
        {
            $sub->value = $result->getValue();
            $finalValue = $result->getValue();
            if ($dvalue > 0 && $dvalue != $finalValue)
                $finalValue = intval($finalValue * (100 - $this->getWrongValueCutoff()) / 100);
            $this->balance->add($uid, $finalValue, "Topup:" . $mapping);
            event(new UserTopup($uid, 0, $finalValue, $mapping));
        }
        $sub->save();
        if (!$sub->success)
        {
            $this->setLastErrorTs($uid);
        }
        $this->clearTopupSession($uid);
        return $sub;
    }
    
    public function getWrongValueCutoff()
    {
        return config('payment.declare_wrong_cutoff', 0);
    }
    
    public function recaptcha($uid)
    {
        $route = $this->getTopupSession($uid);
        if (empty($route))
            return __('hanoivip::topup.timeout');
        Log::debug('TopupService recaptcha current route info:' . print_r($route, true));
        $session = $route['session'];
        $ret = GateFacade::refresh($session);
        if (!$ret['available'])
            return __('hanoivip::topup.channel-maintain');
        if ($ret['busy'])
            return __('hanoivip::topup.channel-unavailable');
        if (empty($ret['pid']))
            throw new Exception('Topup partner ID not determined yet!');
        return $ret;
    }
    
    public function cancel($uid)
    {
        $route = $this->getTopupSession($uid);
        if (empty($route))
            return false;
        $session = $route['session'];
        GateFacade::cancel($session);
        $this->clearTopupSession($uid);
        return true;
    }
    
    public function handle(DelayCard $event)
    {
        return $this->callback($event->mapping, $event->value, $event->turnFailed);
    }
}