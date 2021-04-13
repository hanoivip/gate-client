<?php

namespace Hanoivip\GateClient\Contracts;

interface IGateClient
{
    public function status();
    
    public function check($type, $serial, $password, $mapping);
    
    /**
     * Lấy các thông tin chuẩn bị nạp loại thẻ nhất định
     * + Các thông tin routing
     * + Các tham số nếu cần
     * 
     * @param string $type
     * @param number $dvalue
     * @return array
     */
    public function prepare($type, $dvalue);
    
    /**
     * Nạp thẻ 2 bước (1 chuẩn bị, 2 nạp)
     */
    public function checkPrerouted($type, $dvalue, $serial, $password, 
        $mapping, $pid, $sid = null, $captcha = null);
    
    public function recaptcha($type, $pid, $sid, $dvalue);
    
    public function cancel($pid, $sid);
}