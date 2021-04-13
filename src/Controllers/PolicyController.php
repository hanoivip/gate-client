<?php

namespace Hanoivip\GateClient\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\GateClient\Services\PolicyService;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    private $policies;
    
    public function __construct(PolicyService $policies)
    {
        $this->policies = $policies;
    }
    
    public function list()
    {
        $list = $this->policies->getSystemPolicies();
        return view('hanoivip::admin.policy', ['policies' => $list]);
    }
    
    public function newUI()
    {
        return view('hanoivip::admin.policy-new');
    }
    
    public function new(Request $request)
    {
        $type = $request->get('type');
        $title = $request->get('title');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $params = $request->get('params');
        $result = $this->policies->newSystemPolicy($type, $title, $startTime, $endTime, $params);
        if ($result === true)
        {
            return view('hanoivip::admin.policy-result', ['message' => __('hanoivip::admin.policy.success')]);
        }
        else
        {
            return view('hanoivip::admin.policy-result', ['error_message' => __('hanoivip::admin.policy.error' . $result)]);
        }
    }
    
    public function delete(Request $request)
    {
        $pid = $request->get('pid');
        $result = $this->policies->delete($pid);
        if ($result === true)
            return view('hanoivip::admin.policy-result', ['message' => __('hanoivip::admin.policy.del-success')]);
        else
            return view('hanoivip::admin.policy-result', ['error_message' => __('hanoivip::admin.policy.del-error' . $result)]);
    }
}