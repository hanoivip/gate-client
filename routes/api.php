<?php

use Illuminate\Support\Facades\Route;

// Private APIs
Route::middleware('auth:api')->prefix('api')->namespace('Hanoivip\GateClient\Controllers')->group(function () {
    // Xem thông tin tài khoản
    Route::any('/topup/info', 'BalanceController@info');
    // List all payment methods
    Route::any('/topup', 'TopupController@topupUI2');//step 1
    Route::any('/topup/select', 'TopupController@selectType');//step 2
    Route::any('/topup/do', 'TopupController@topup2');//result
    // Lịch sử chung
    Route::any('/topup/history', 'TopupController@history');
    // Lịch sử nạp
    Route::any('/topup/historyP', 'TopupController@topupHistory');
    // Lịch sử chuyển xu
    Route::any('/topup/historyR', 'TopupController@rechargeHistory');
});

// Public APIs
Route::prefix('api')->namespace('Hanoivip\GateClient\Controllers')->group(function () {
    // Lấy xếp hạng tài phú
    Route::get('/topup/rank/global', 'TopupController@globalRank');
    // Lấy xếp hạng tài phú - theo tuần, tháng...
    Route::get('/topup/rank/{key}', 'TopupController@rank');
    // Kiểm tra trạng thái thẻ nạp
    Route::get('/topup/query', 'TopupController@query');
    Route::get('/topup/rule', 'TopupController@getRule')->name('topup.rule');
    Route::get('/topup/lang', 'TopupController@getLang')->name('topup.lang');
});

