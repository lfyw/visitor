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
    public function __construct(public $idCard, public ?array $gates = null)
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
        $visitor = Visitor::firstWhere('id_card', $this->idCard)?->loadFiles();

        if (config('app.env') !== 'production') {
            Log::info('【测试环境】临时访客删除下放直接通过', ['id_card' => $this->idCard, 'visitor' => $visitor]);
            return;
        }
        if (!$visitor) {
            return;
        }

        Log::info('【生产环境】访客删除下发', ['id_card' => $this->idCard, 'visitor' => $visitor]);

        if (!$this->gates) {
            $gates = Gate::getByWaysThroughPassageway($visitor->ways)->get(['ip', 'number'])->toArray();
        }

        $parameter = [
            'id_card' => $visitor->id_card,
            'real_name' => $visitor->name,
            'face_picture' => config('app.url') . $visitor->files()->first()?->url,
            'access_date_from' => $visitor->access_date_from,
            'access_date_to' => $visitor->access_date_to,
            'access_time_from' => $visitor->access_time_from,
            'access_time_to' => $visitor->access_time_to,
            'limiter' => $visitor->limiter,
            'gate' => $gates,
        ];

        Http::timeout(60)->post(Constant::getDelUserUrl(), $parameter);
    }
}
