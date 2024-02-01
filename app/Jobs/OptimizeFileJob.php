<?php

namespace App\Jobs;

use Exception;
use ImageOptimizer;
use App\Models\Attachment;
use App\Helpers\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OptimizeFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $filepath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Copy file from default disk to temp disk
        Storage::disk('temp')->put($this->filepath, Storage::get($this->filepath));

        $origitalFilePath = Storage::path($this->filepath);
        $fullTempFilePath = Storage::disk('temp')->path($this->filepath);

        if (Storage::mimeType($this->filepath) == 'application/pdf') {
            $output = shell_exec("ps2pdf14 -dPDFSETTINGS=/screen '" . $origitalFilePath . "' '" . $fullTempFilePath . "'");
        } else {
            ImageOptimizer::optimize($fullTempFilePath);
        }

        // Compare size
        if (Storage::disk('temp')->size($this->filepath) < Storage::size($this->filepath)) {
            // Write the compressed file back to default disk
            Storage::put($this->filepath, Storage::disk('temp')->get($this->filepath));
        }

        // Delete temp file
        Storage::disk('temp')->delete($this->filepath);
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
