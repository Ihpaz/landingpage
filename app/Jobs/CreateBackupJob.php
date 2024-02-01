<?php

namespace App\Jobs;

use Exception;
use App\Helpers\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    protected $option;
    public $timeout = 60 * 10;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($option = '')
    {
        $this->option = $option;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $config = config('backup');
        $config['backup']['source']['files']['include'] = storage_path('app');
        $config['backup']['source']['files']['exclude'] = [
            storage_path('app/' . $config['backup']['name']),
            storage_path('app/backup-temp')
        ];

        $backupJob = BackupJobFactory::createFromArray(($this->option == 'only-storage') ? $config : config('backup'));
        if ($this->option === 'only-db') {
            $backupJob->dontBackupFilesystem();
        }
        if (($this->option === 'only-files') || ($this->option === 'only-storage')) {
            $backupJob->dontBackupDatabases();
        }
        if (!empty($this->option)) {
            $prefix = str_replace('_', '-', $this->option) . '-';
            $backupJob->setFilename($prefix . date('Ymd-His') . '.zip');
        }
        $backupJob->run();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Sentry exception
        ActivityLog::sentry($exception);
    }
}
