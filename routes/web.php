<?php
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    'auth:web'
])->namespace('Hanoivip\GateClient\Controllers')->group(function () {
    // gate default path
    Route::get('/topup', 'TopupController@topupUI2');
    Route::get('/history', 'TopupController@history')->name('history');
    // new gate version
    Route::get('/topup/step1', 'TopupController@topupUI2')->name('topup');
    Route::get('/topup/step2', 'TopupController@selectType')->name('topup.by.type');
    Route::post('/topup/result', 'TopupController@topup2')->name('webTopup');
    Route::get('/topup/recaptcha', 'TopupController@recaptcha')->name('topup.recaptcha');
    Route::get('/topup/cancel', 'TopupController@cancel')->name('topup.cancel');
    // reactjs UI
    Route::get('/jtopup', 'TopupController@jsTopup')->name('jtopup');
    Route::get('/jhistory', 'TopupController@jsHistory')->name('jhistory');
    Route::get('/jrecharge', 'TopupController@jsRecharge')->name('jrecharge');
    // tracking topup
    Route::get('/topup/success', 'TopupController@onTopupSuccess')->name('topup.success');
});

Route::middleware([
    'web',
    'admin'
])->namespace('Hanoivip\GateClient\Controllers')
    ->prefix('ecmin')
    ->group(function () {
    // List all current policies
    Route::get('/policy', 'PolicyController@list')->name('ecmin.policy');
    Route::get('/policy/new', 'PolicyController@newUI')->name('ecmin.policy.new');
    Route::post('/policy/new', 'PolicyController@new')->name('ecmin.policy.new.do');
    Route::post('/policy/del', 'PolicyController@delete')->name('ecmin.policy.delete.do');
    // Statisic income
    Route::get('/income', 'Statistics@stat')->name('ecmin.income');
    Route::get('/income/today', 'Statistics@today')->name('ecmin.income.today');
    Route::get('/income/month', 'Statistics@thisMonth')->name('ecmin.income.thisMonth');
    Route::post('/income', 'Statistics@statByTime')->name('ecmin.income.byTime');
    // User topup history
    Route::post('/topup/history', 'AdminController@history')->name('ecmin.topup.history');
});