<?php

namespace Hanoivip\GateClient\Commands;

use Illuminate\Console\Command;
use Hanoivip\Events\Gate\UserTopup;

class TestTopup extends Command
{
    protected $signature = 'test:topup {uid}';
    
    protected $description = 'Test of topup';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle()
    {
        $uid = $this->argument('uid');
        event(new UserTopup($uid, 'VTT', 10000, "8888888"));
    }
}
