<?php

namespace Glumbo\Gracart\Commands;

use Illuminate\Console\Command;
use Throwable;
use DB;

class Restore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sc:restore {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database "sc:restore --path=abc.sql"';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->option('path');
        if (!$path) {
            echo json_encode(['error' => 1, 'msg' => 'Path empty']);
            exit();
        } else {
            $pathFull = storage_path() . "/backups/" . $path;
            if (!file_exists($pathFull)) {
                echo  json_encode(['error' => 1, 'msg' => 'File not found']);
                exit();
            } else {
                try {
                    DB::connection(GC_CONNECTION)->transaction(function () use ($pathFull) {
                        DB::connection(GC_CONNECTION)->unprepared(file_get_contents($pathFull));
                        echo json_encode(['error' => 0, 'msg' => gc_language_render('admin.backup.restore_success')]);
                        exit();
                    });
                } catch (Throwable $e) {
                    gc_report($e->getMessage());
                    echo  json_encode(['error' => 1, 'msg' => $e->getMessage()]);
                    exit();
                }
            }
        }
    }
}
