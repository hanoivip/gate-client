<?php

namespace Hanoivip\GateClient\Contracts;

interface IBalance
{
    /**
     * Lấy thông tin các loại tài khoản.
     *
     * @param number $uid
     * @return array Array of balance records
     */
    public function getInfo($uid);
    
    /**
     * Kiểm tra xem có đủ xu trong tài khoản không
     *
     * @param number $uid
     * @param number $coin
     * @param number $type
     * @return boolean
     */
    public function enough($uid, $coin, $type = 0);
    
    public function add($uid, $value, $reason, $type = 0);
    
    public function remove($uid, $value, $reason, $type = 0);
    
    public function getHistory($uid);
}