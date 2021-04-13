<?php

namespace Hanoivip\GateClient\Services;

use Illuminate\Support\Facades\Log;

class TestGateResponseParser
{
    protected  $returned;
    
    protected $explains = [
        "RESULT:11@" => "Thẻ trễ, xin đợi thêm vài phút sẽ có kết quả.!",
        "RESULT:06@" => "Thẻ không tồn tại",
        "RESULT:05@" => "Thẻ đã bị sử dụng",
        "RESULT:02@" => "Loại thẻ không hợp lệ",
        "RESULT:03@" => "Sai mã thẻ",
        "RESULT:12@" => "Sai seri thẻ",
        "RESULT:08@" => "Thẻ đã nạp, không thể nạp lại",
        "RESULT:00@" => "Dịch vụ gạch thẻ đang bảo trì. Vui lòng thử lại sau.",
        "RESULT:13@" => "Gõ sai mã captcha",
        "RESULT:14@" => "Thẻ này chưa dùng được ngay, thử lại sau 30 phút.",
        "RESULT:15@" => "Cổng nạp thẻ đang bận, thử lại sau 1-2 phút.",
        "RESULT:99@" => "Lỗi hệ thống. Liên hệ GM giải quyết.",
    ];
    
    public function __construct($returned)
    {
        $this->returned = $returned[0];
    }
    
    public function isSuccess()
    {
        return true;
    }
    
    public function isDelay()
    {
        return false;
    }
    
    public function getValue()
    {
        return 10000;
    }
    
    public function getExplainMessage()
    {
        if (isset($this->explains[$this->returned]))
            return $this->explains[$this->returned];
        Log::error("Unknown response code:" . $this->returned);
        return "Lỗi hệ thống. Liên hệ GM. (3)";
    }
}