<?php

namespace App\Jobs;

use App\Models\Gate;
use App\Models\Visitor;
use App\Supports\Sdks\Constant;
use App\Supports\Sdks\VisitorIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PullIssue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public $idCard,
        public $name,
        public $facePicture,
        public $accessDateFrom,
        public $accessDateTo,
        public $accessTimeFrom,
        public $accessTimeTo,
        public $limiter,
        public $ways,
        public ?array $gates = null
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (config('app.env') !== 'production') {
            Log::info('【测试环境】临时访客删除下放直接通过', [
                'id_card' => $this->idCard,
                'name' => $this->name,
                'face_picture' => $this->facePicture,
                'access_date_from' => $this->accessDateFrom,
                'access_date_to' => $this->accessDateTo,
                'access_time_from' => $this->accessTimeFrom,
                'access_time_to' => $this->accessTimeTo,
                'limiter' => $this->limiter,
                'ways' => $this->ways,
                'gates' => $this->gates
            ]);
            return;
        }

        Log::info('【生产环境】访客删除下发', [
            'id_card' => $this->idCard,
            'name' => $this->name,
            'face_picture' => $this->facePicture,
            'access_date_from' => $this->accessDateFrom,
            'access_date_to' => $this->accessDateTo,
            'access_time_from' => $this->accessTimeFrom,
            'access_time_to' => $this->accessTimeTo,
            'limiter' => $this->limiter,
            'ways' => $this->ways,
            'gates' => $this->gates
        ]);

        if (!$this->gates) {
            $gates = Gate::getByWaysThroughPassageway($this->ways)->get(['ip', 'number'])->toArray();
        }

        $parameter = [
            'id_card' => $this->idCard,
            'real_name' => $this->name,
            'face_picture' => config('app.url') . $this->facePicture,
            'access_date_from' => $this->accessDateFrom,
            'access_date_to' => $this->accessDateTo,
            'access_time_from' => $this->accessTimeFrom,
            'access_time_to' => $this->accessTimeTo,
            'limiter' => $this->limiter,
            'gate' => $gates,
        ];

        Http::timeout(60)->post(Constant::getDelUserUrl(), $parameter);
    }
}
