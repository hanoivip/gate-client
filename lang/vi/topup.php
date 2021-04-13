<?php

return [
    'ui' => [
        'history' => 'Lịch sử nạp',
        'notice' => 'Giá trị đã khai báo: :dvalue (chú ý báo sai sẽ mất :cutoff% giá trị thật, nếu sai hãy click [Làm lại])',
        'serial' => 'Seri thẻ',
        'password' => 'Mã thẻ',
        'topupbtn' => 'Nạp',
        'rechargebtn' => 'Chuyển xu',
        'cancelbtn' => 'Làm lại',
        'newtopupbtn' => 'Nạp nữa',
        'txt_rule' => 'Quy tắc',
        'btn_next' => 'Tiếp',
        'lbl_choose_value' => 'Chọn mệnh giá sẽ nạp',
        'lbl_rule_title' => 'Chi tiết quy tắc',
        'btn_close' => 'Đóng',
        'btn_back' => 'Quay lại',
        'btn_topup' => 'Nạp',
        'btn_retopup' => 'Nạp lại'
    ],
    'all-unavaiable' => 'Tất cả các kênh đều đang bảo trì hoặc bận. Mời thử lại sau 10 phút@', 
    'system-error' => 'Có lỗi xảy ra. Liên hệ hỗ trợ giải quyết.',
    'success-wrong-value' => 'Chọn sai giá trị nên giá trị thực nhận giảm :cutoff %!!',
    'channel-maintain' => 'Kênh nạp cho loại thẻ tương ứng đang bảo trì. Thử lại sau 15 phút.',
    'channel-unavailable' => 'Kênh nạp đang bận. Bạn hãy đợi từ 3-5 phút rồi thử lại.',
    'card-value-empty' =>  'Bạn phải chọn giá trị thẻ trước!',
    'timeout' => 'Quá thời gian thực hiện, mời thực hiện lại.',
    'delay' => 'Thẻ trễ. Cần đợi thêm vài phút để kiểm tra thẻ.',
    'success' => 'Nạp thẻ thành công với mệnh giá :value',
    'rule' => '
<p>1. Phải chọn trước giá trị thẻ, nếu được yêu cầu </p>
<p>2. Chọn sai giá trị:</p>
<p>+ Chọn sai nhỏ hơn giá trị thật: tính giá trị đã khai và phạt 50% giá trị đã khai.</p>
<p>+ Chọn sai lớn hơn giá trị thật: tính giá trị thật và phạt 50% giá trị thật.
<br/>
<p>Chú ý 1: Chiết khấu từng loại thẻ sẽ thay đổi theo thị trường, cập nhật liên tục, hãy kiểm tra trước khi nạp.<p>
<p>Chú ý 2: Các thẻ không thông báo chiết khấu thì mặc định là 0% (100k được 100k)<p>',
    'status' => [
        0 => 'Đúng',
        1 => 'Sai',
        2 => 'Trễ',
        3 => 'S.giá',
    ]
];