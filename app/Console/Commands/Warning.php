<?php

namespace App\Console\Commands;

use App\Jobs\PullIssue;
use App\Models\Rule;
use App\Models\Scene;
use App\Models\UserType;
use App\Models\Warning as WarningModel;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class Warning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warning:no_out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculate warning';

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
     * @return int
     */
    public function handle()
    {
        info('超时未出预警启动...');
        UserType::all()->each(function (UserType $userType){
            info(sprintf("超时未出预警：【%s】进程启动...", $userType->name));
            $this->runUserNotOut($userType->name);
        });
    }

    protected function runUserNotOut($userType)
    {
        $duration = $this->getDuration($userType);
        Scene::whereHas('visitor', function (Builder $builder) use ($userType){
            $builder->whereHas('userAsVisitor', function (Builder $userBuilder) use ($userType){
                $userBuilder->whereHas('userType', function (Builder $userTypeBuilder) use ($userType){
                    $userTypeBuilder->where('name', $userType);
                });
            });
        })->each(function (Scene $scene) use ($duration, $userType){
            $diffInHour = now()->diffInHours($scene->passed_at);
            if($diffInHour >= $duration){
                //与当前时差超过规则，添加到预警库
                info(sprintf("超时未出预警 => 检测到符合条件的预警 预警人员类型：%s 预警人员身份证号：%s 预警人员姓名：%s" ,
                    $userType,
                    sm4decrypt($scene->visitor->id_card),
                    $scene->visitor->name)
                );
                $warningHasExists = WarningModel::onlyToday()->where('id_card', $scene->visitor->id_card)->exists();
                if ($warningHasExists){
                    info(sprintf("超时未出预警 => 检测到当日已存在预警信息，不再重复预警 预警人员类型：%s 预警人员身份证号：%s 预警人员姓名：%s" ,
                            $userType,
                            sm4decrypt($scene->visitor->id_card),
                            $scene->visitor->name)
                    );
                    return ;
                }

                info(sprintf("超时未出预警 => 检测到符合条件的预警，添加入预警库 预警人员类型：%s 预警人员身份证号：%s 预警人员姓名：%s" ,
                        $userType,
                        sm4decrypt($scene->visitor->id_card),
                        $scene->visitor->name)
                );

                WarningModel::create([
                    'name' => $scene->visitor->name,
                    'type' => $scene->visitor->getType(),
                    'gender' => $scene->visitor->gender,
                    'age' => $scene->visitor->age,
                    'id_card' => $scene->visitor->id_card,
                    'phone' => $scene->visitor->phone,
                    'unit' => $scene->visitor->unit,
                    'user_real_name' => $scene->visitor->getUserName(),
                    'user_department' => $scene->visitor->getUserDepartment(),
                    'reason' => $scene->visitor->reason,
                    'access_date_from' => $scene->visitor->access_date_from,
                    'access_date_to' => $scene->visitor->access_date_to,
                    'ways' => $scene->visitor->ways->pluck('name')->implode(','),
                    'gate_name' => $scene?->gate->name,
                    'gate_ip' => $scene?->gate->ip,
                    'access_time_from' => $scene->visitor->access_time_from,
                    'access_time_to' => $scene->visitor->access_time_to,
                    'limiter' => $scene->visitor->limiter,
                    'relation' => $scene->visitor->relation,
                    'warning_type' => '超时未出',
                    'warning_at' => now(),
                    'visitor_id' => $scene->visitor->id
                ]);
                PullIssue::dispatch(
                    sm4decrypt($scene->visitor->id_card),
                    $scene->visitor->name,
                    $scene->visitor->files->first()?->url,
                    $scene->visitor->access_date_from,
                    $scene->visitor->access_date_to,
                    $scene->visitor->access_time_from,
                    $scene->visitor->access_time_to,
                    $scene->visitor->limiter,
                    $scene->visitor->ways
                )->onQueue('issue');
            }
        });
    }

    private function getDuration($userType)
    {
        $rule = Rule::firstWhere('name', '规则设置');
        if (!$rule) {
            return 0;
        }
        $notOutRule = $rule->value['not_out'];
        $runUserTypeId = UserType::firstWhere('name', $userType)?->id;
        $runUserNotRule = current(Arr::where($notOutRule, fn($item) => $item['user_type_id'] == $runUserTypeId));
        return data_get($runUserNotRule, 'duration');
    }
}
