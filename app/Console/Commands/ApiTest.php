<?php

namespace App\Console\Commands;

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Models\Auditor;
use App\Models\Issue;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lfyw\FileManager\Models\File;

class ApiTest extends Command
{
    protected $host;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $method = $this->argument('method');
        $this->host = config('app.url');
        return call_user_func_array([$this, $method], []);
    }

    public function uploadFile()
    {
        $path = '/api/pc/files';
        $url = $this->host . $path;
        try {
            $response = Http::attach(
                'file', file_get_contents(storage_path('test.jpg')), 'test.jpg'
            )->post($url);
            Log::debug('test-api:uploadFile', [
                'response' => $response->json()
            ]);
            return $response->json();
        }catch (\Exception $exception){
            Log::debug('test-api:uploadFile', [
                'error' => $exception->getMessage()
            ]);
        }

    }

    public function postAudit()
    {
        $path = '/api/audit';
        $url = $this->host . $path;

        Audit::truncate();
        Auditor::truncate();
        \DB::table('audit_way')->truncate();
        Visitor::truncate();
        \DB::table('visitor_way')->truncate();
//        Issue::truncate();


        $fileId = ($file = File::first()) ? $file->id : $this->uploadFile()['id'];

        $response  = Http::post($url, [
            'name' => '测试',
            'id_card' => '110101199003077176',
            'phone' => '18633237875',
            'unit' => '单位部门',
            'user_id' => 1,
            'visitor_type_id' => 1,
            'way_ids' => [1],
            'access_date_from' => "2022-4-5",
            'access_date_to' => "2022-4-7",
            "reason" => '访问事由',
            'relation' => '父母',
            "face_picture_ids" => [$fileId]
        ]);
        Log::debug('test-api:postAudit', [
            'response' => $response->json()
        ]);
    }

    public function auditPass()
    {
        $path = '/api/pc/audits/1';
        $url = $this->host . $path;

        $token = $this->login()['token'];
        $audit = Audit::firstWhere(['id' => 1]);
        $param = [
            'name' => $audit->name,
            'id_card' => $audit->id_card,
            'phone' => $audit->phone,
            'unit' => $audit->unit,
            'user_id' => $audit->user_id,
            'visitor_type_id' => $audit->visitor_type_id,
            'way_ids' => $audit->ways->pluck('id')->toArray(),
            'access_date_from' => $audit->access_date_from,
            'access_date_to' => $audit->access_date_to,
            "reason" => $audit->reason,
            'relation' => $audit->relation,
            "face_picture_ids" => $audit->files->pluck('id')->toArray(),

            'audit_status' => AuditStatus::PASS->value,
            'access_time_from' => '9:00',
            'access_time_to' => '18:00',
            'limiter' => 2
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer '. $token])->put($url, $param);

        Log::debug('test-api:auditPass', [
            'response' => $response->json()
        ]);
    }

    public function login()
    {
        $path = '/api/pc/authorizations';
        $url = $this->host . $path;

        $user = User::first();
        $param = [
            'name' => $user->name,
            'password' => 'admin123456'
        ];

        $response = Http::post($url, $param);
        Log::debug('test-api:login', [
            'response' => $response->json()
        ]);
        return $response->json();
    }
}
