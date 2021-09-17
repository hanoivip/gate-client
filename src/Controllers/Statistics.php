<?php

namespace Hanoivip\GateClient\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hanoivip\GateClient\Models\Submission;

class Statistics extends Controller
{
    public function stat()
    {
        return view('hanoivip::admin.income');
    }
    
    public function today()
    {
        $startTime = date('Y-m-d H:i:s', strtotime('today midnight'));
        $endTime = date('Y-m-d H:i:s', strtotime('tomorrow'));
        $sum = $this->sumIncome($startTime, $endTime);
        return view('hanoivip::admin.income-result', ['sum' => $sum]);
    }
    
    public function thisMonth()
    {
        $startTime = date('Y-m-d H:i:s', strtotime('first day of this month midnight'));
        $endTime = date('Y-m-d H:i:s', strtotime('first day of next month midnight'));
        $sum = $this->sumIncome($startTime, $endTime);
        return view('hanoivip::admin.income-result', ['sum' => $sum]);
    }
    
    public function statByTime(Request $request)
    {
        $startTime = $request->get('start_time') . ' 00:00:00';
        $endTime = $request->get('end_time') . ' 23:59:59';
        $sum = $this->sumIncome($startTime, $endTime);
        return view('hanoivip::admin.income-result', ['sum' => $sum]);
    }
    
    private function sumIncome($startTime, $endTime)
    {
        $sum = Submission::where('created_at', '>=', $startTime)
        ->where('created_at', '<', $endTime)
        ->sum('final_value');
        return $sum;
    }
}