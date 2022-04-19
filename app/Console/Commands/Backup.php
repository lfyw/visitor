<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:db {note?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup db';

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
        $dir = storage_path('app/public/backups');
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $date = date('YmdHis');
        $fileName = sprintf('%s-%s.sql.gz', $date, config('database.connections.mysql.database'));

        $process = Process::fromShellCommandline(sprintf(
            'pg_dump -U %s -d %s | gzip > %s/%s',
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.database'),
            $dir,
            $fileName
        ))->setTimeout(3600);
        $status = $process->run();

        if ($status === 0) {
            \Log::info('备份数据库成功', ['date' => date('Y-m-d H:i:s')]);
            $size = \File::size($dir . '/' . $date . '-' . config('database.connections.mysql.database') . '.sql.gz');

            \App\Models\Backup::create([
                'name' => $fileName,
                'size' => round($size / 1024 / 1024, 2),
                'note' => $this->argument('note') ?: '系统自动备份'
            ]);
            $this->output->success(sprintf('备份数据库成功..., 文件名: %s, 大小: %sM', $fileName, round($size / 1024 / 1024, 2)));
        } else {
            \Log::error('备份数据库失败', ['date' => date('Y-m-d H:i:s')]);
            $this->output->error('备份数据库失败...');
        }
    }
}
