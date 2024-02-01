<?php

namespace App\Jobs;

use App\Imports\UserImport;
use App\Jobs\SendUserNotification;
use App\Notifications\DatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;

class UserImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $file;
    protected $user;
    public $timeout = 600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $file)
    {
        $this->user = $user;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Notification
        $notifs = ['message' => 'Completed user import job.'];
        Excel::import(new UserImport, Storage::disk('temp')->path('import/' . $this->file), null, \Maatwebsite\Excel\Excel::XLSX)
            ->chain([
                new SendUserNotification($this->user, new DatabaseNotification($notifs))
            ]);
        unlink(Storage::disk('temp')->path('import/' . $this->file));
    }
}
