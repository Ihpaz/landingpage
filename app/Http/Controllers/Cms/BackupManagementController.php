<?php

namespace App\Http\Controllers\Cms;

use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Jobs\CreateBackupJob;
use App\Rules\PathToZip;
use App\Rules\BackupDisk;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class BackupManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms backup-management view',   ['only' => ['index', 'download']]);
        $this->middleware('permission:cms backup-management create', ['only' => ['store']]);
        $this->middleware('permission:cms backup-management delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Backup Management';
        // END MANDATORY PARAMETER

        $data['monitor'] = $this->monitor();

        return view('cms.backup.index', $data);
    }

    public function monitor()
    {
        return Cache::remember('backup-statuses', now()->addSeconds(4), function () {
            return BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitor_backups'))
                ->map(function (BackupDestinationStatus $backupDestinationStatus) {
                    return [
                        'name' => $backupDestinationStatus->backupDestination()->backupName(),
                        'disk' => $backupDestinationStatus->backupDestination()->diskName(),
                        'reachable' => $backupDestinationStatus->backupDestination()->isReachable(),
                        'healthy' => $backupDestinationStatus->isHealthy(),
                        'amount' => $backupDestinationStatus->backupDestination()->backups()->count(),
                        'newest' => $backupDestinationStatus->backupDestination()->newestBackup()
                            ? $backupDestinationStatus->backupDestination()->newestBackup()->date()->diffForHumans()
                            : __('No backups present'),
                        'usedStorage' => Format::humanReadableSize($backupDestinationStatus->backupDestination()->usedStorage()),
                    ];
                })
                ->values()
                ->first();
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $option = $request->option;
            dispatch(new CreateBackupJob($option));
            $message = $option ?? 'full backup';
            
            // Send session flash message
            ActivityLog::log('Backup', 'Creating a new backup <b>' . $message . '</b> manually');
            Alert::success(trans('common.success'), 'Creating a new backup in the background...')->autoclose(5000);
            return redirect()
                ->route('cms.backup.index');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'disk' => new BackupDisk(),
            'path' => ['required', new PathToZip()],
        ]);

        try {
            $backupDestination = BackupDestination::create($validated['disk'], config('backup.backup.name'));
            $backupDestination
                ->backups()
                ->first(function (Backup $backup) use ($validated) {
                    return $backup->path() === $validated['path'];
                })
                ->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Backup telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function download(Request $request)
    {
        $validated = $request->validate([
            'disk' => new BackupDisk(),
            'path' => ['required', new PathToZip()],
        ]);
        $backupDestination = BackupDestination::create($validated['disk'], config('backup.backup.name'));
        $backup = $backupDestination->backups()->first(function (Backup $backup) use ($validated) {
            return $backup->path() === $validated['path'];
        });

        if (!$backup) {
            return response('Backup not found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $fileName = pathinfo($backup->path(), PATHINFO_BASENAME);
        $downloadHeaders = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'application/zip',
            'Content-Length' => $backup->size(),
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'public',
        ];
        return response()->stream(function () use ($backup) {
            $stream = $backup->stream();
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $downloadHeaders);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $backupDestination = BackupDestination::create('local', config('backup.backup.name'));
        $query =  Cache::remember("backups-{'local'}", now()->addSeconds(4), function () use ($backupDestination) {
            return $backupDestination
                ->backups()
                ->map(function (Backup $backup) {
                    return [
                        'path' => $backup->path(),
                        'date' => $backup->date()->format('Y-m-d H:i:s'),
                        'size' => Format::humanReadableSize($backup->size()),
                    ];
                })
                ->toArray();
        });

        $data = datatables()->of($query)
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('cms backup-management view')) {
                    array_push($action, '<a href="' . route('cms.api.backup.download') . '?path=' . $data['path'] . '&disk=local" class="btn btn-secondary btn-xs"><i class="fa fa-cloud-download"></i></a>');
                }
                if (auth()->user()->can('cms backup-management delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="deleteRecord(\'' . $data['date'] . '\',\'' . $data['path'] . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['actions'])
            ->make(true);
        return $data;
    }
}
