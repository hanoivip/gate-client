<?php

namespace Hanoivip\GateClient\Commands;

use Illuminate\Console\Command;

class PolicyNew extends Command
{
    protected $signature = 'policy:new {type} {title} {params}';
    
    protected $description = 'New policy';
    

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle()
    {
    }
}
