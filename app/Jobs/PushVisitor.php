<?php

namespace App\Jobs;

use App\Models\Gate;
use App\Models\Issue;
use App\Models\Passageway;
use App\Models\Visitor;
use App\Supports\Sdks\Constant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushVisitor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public $idCard,
        public $accessDateFrom,
        public $accessDateTo,
        public $accessTimeFrom,
        public $accessTimeTo,
        public $limiter
    )
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $visitor = Visitor::firstWhere('id_card', $this->idCard);
        Log::info(sprintf('访客【%s】启动下发...', $this->idCard), ['id_card' => $this->idCard, 'visitor' => $visitor]);

        $passageways = Passageway::getByWays($visitor->ways)->get();
        $gates = Gate::getByPassageways($passageways)->get();
        $gatesFormat = Gate::getByPassageways($passageways)->get(['ip', 'number'])->toArray();
        $facePicture = $visitor->files()->first();
        if (!$facePicture) {
            Log::info(sprintf('访客【%s】面容照片不存在，停止下发!', $this->idCard), ['id_card', $this->idCard, 'visitor' => $visitor]);
            return ;
        }

        if (config('app.env') !== 'production') {
            Log::info('【测试环境】访客下放直接通过', ['id_card' => $this->idCard, 'visitor' => $visitor]);
            $gates->each->createIssue($visitor->id_card, true);
            Issue::syncIssue($visitor->id_card);
        } else {
            try {
                $parameter = [
                    'id_card' => $visitor->id_card,
                    'real_name' => $visitor->name,
                    'face_picture' => config('app.url') . $facePicture->url,
                    'access_date_from' => $this->accessDateFrom,
                    'access_date_to' => $this->accessDateTo,
                    'access_time_from' => $this->accessTimeFrom,
                    'access_time_to' => $this->accessTimeTo,
                    'limiter' => $this->limiter,
                    'gate' => $gatesFormat,
                ];
                $response = Http::timeout(5)->post(Constant::getSetUserUrl(), $parameter);
                $response->throw();
                $gates->each->createIssue($visitor->id_card, true);
                Issue::syncIssue($visitor->id_card);
                $visitor->fill([
                    'actual_pass_count' => 0,
                    'limiter' => $this->limiter,
                    'access_date_from' => $this->accessDateFrom,
                    'access_date_to' => $this->accessDateTo,
                    'access_time_from' => $this->accessTimeFrom,
                    'access_time_to' => $this->accessTimeTo
                ])->save();
                Log::info('【生产环境】访客下发成功:', ['body' => $response->body(), 'json' => $response->json(), 'visitor' => $visitor, 'id_card' => $this->idCard]);
            } catch (\Exception $exception) {
                Log::error('【生产环境】下发异常:' . $exception->getMessage());
                //失败则记录下发失败记录
                $gates->each->createIssue($visitor->id_card, false);
                Issue::syncIssue($visitor->id_card);
            }
        }
    }

}
