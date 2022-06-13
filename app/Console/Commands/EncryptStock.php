<?php

namespace App\Console\Commands;

use App\Models\Audit;
use App\Models\Blacklist;
use App\Models\PassingLog;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EncryptStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrypt:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt stock phone and id_card';

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
        DB::beginTransaction();
        try {
            //1.更新审核表
            $this->info('更新【audit】表...');
            $audits = Audit::all();
            $auditBar = $this->output->createProgressBar($audits->count());
            foreach ($audits as $audit){
                $audit->fill([
                    'id_card' => sm4encrypt($audit->id_card),
                    'phone' => sm4encrypt($audit->phone)
                ])->save();
                $auditBar->advance();
            }
            $auditBar->finish();
            //2.更新黑名单表
            $this->info('更新【blacklists】表...');
            $blacklists = Blacklist::all();
            $blacklistBar = $this->output->createProgressBar($blacklists->count());
            foreach ($blacklists as $blacklist){
                $blacklist->fill([
                    'id_card' => sm4encrypt($blacklist->id_card),
                    'phone' => sm4encrypt($blacklist->phone)
                ])->save();
                $blacklistBar->advance();
            }
            $blacklistBar->finish();
            //3.更新访问日志表
            $this->info('更新【passing_logs】表...');
            $passingLogs = PassingLog::all();
            $passingLogBar = $this->output->createProgressBar($passingLogs->count());
            foreach ($passingLogs as $passingLog){
                $passingLog->fill([
                    'id_card' => sm4encrypt($passingLog->id_card),
                    'phone' => sm4encrypt($passingLog->phone)
                ])->save();
                $passingLogBar->advance();
            }
            $passingLogBar->finish();
            //4.更新人员表
            $this->info('更新【users】表...');
            $users = User::all();
            $usersBar = $this->output->createProgressBar($users->count());
            foreach ($users as $user){
                $user->fill([
                    'id_card' => sm4encrypt($user->id_card),
                    'phone_number' => sm4encrypt($user->phone_number)
                ])->save();
                $usersBar->advance();
            }
            $usersBar->finish();
            //5.更新访客表
            $this->info('更新【visitors】表...');
            $visitors = Visitor::all();
            $visitorsBar = $this->output->createProgressBar($visitors->count());
            foreach ($visitors as $visitor){
                $visitor->fill([
                    'id_card' => sm4encrypt($visitor->id_card),
                    'phone' => sm4encrypt($visitor->phone)
                ])->save();
                $visitorsBar->advance();
            }
            $visitorsBar->finish();
            //6.更新预警表
            $this->info('更新【warnings】表...');
            $warnings = \App\Models\Warning::all();
            $warningsBar = $this->output->createProgressBar($warnings->count());
            foreach ($warnings as $warning){
                $warning->fill([
                    'id_card' => sm4encrypt($warning->id_card),
                    'phone' => sm4encrypt($warning->phone)
                ])->save();
                $warningsBar->advance();
            }
            $warningsBar->finish();
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage()
            ]);
        }
    }
}
