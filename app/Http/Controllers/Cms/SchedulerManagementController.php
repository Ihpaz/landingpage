<?php

namespace App\Http\Controllers\Cms;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Services\Scheduling;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class SchedulerManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms scheduler-management view',   ['only' => ['index']]);
        $this->middleware('permission:cms scheduler-management run',    ['only' => ['run']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Task Scheduler';
        // END MANDATORY PARAMETER
        $data['queue'] = Queue::size();
        $data['queue_attempts'] = DB::table(config('queue.connections.database.table'))
            ->where('attempts', '!=', 0)->count();
        $data['failed'] = DB::table(config('queue.failed.table'))->count();

        return view('cms.scheduler.index', $data);
    }

    /**
     * Run requested task manually.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function run(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required',
        ]);
        try {
            $scheduling = new Scheduling();
            ActivityLog::logWithProperty(
                'Info',
                'Run requested task schedule <strong>' . $request->name . '</strong> manually',
                $scheduling->showTask(Hashids::decode($request->id)[0])
            );
            // Run task
            $scheduling->runTask(Hashids::decode($request->id)[0]);

            return ResponseFormat::ok('Success run ' . $request->name);
        } catch (\Exception $e) {
            $message['error'] = $e->getMessage();
            ActivityLog::logWithProperty(
                'Info',
                'Failed run task schedule <strong>' . $request->name . '</strong>',
                $message
            );
            return ResponseFormat::error($e->getMessage());
        }
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $scheduling = new Scheduling();

        $data = datatables()->of($scheduling->getTasks())
            ->addColumn('formated_task', function ($data) {
                return substr(strip_tags($data['task']['name']), 0, 35) . (strlen($data['task']['name']) > 35 ? '...' : '');
            })
            ->addColumn('formated_description', function ($data) {
                return substr(strip_tags($data['description']), 0, 35) . (strlen($data['description']) > 35 ? '...' : '');
            })
            ->addColumn('interval', function ($data) {
                return '<span class="label label-info">' . $data['expression'] . '</span>  ' . $data['readable'];
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('cms scheduler-management run')) {
                    array_push($action, '<button class="btn btn-secondary btn-xs" onclick="runTask(\'' . Hashids::encode($data['id']) . '\',\'' . $data['description'] . '\')" title="Run Manually"><i class="fa fa-play-circle"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['interval', 'actions'])
            ->make(true);
        return $data;
    }

    public function datatableJobFailed(Request $request)
    {
        $query = DB::table(config('queue.failed.table'))
            ->selectRaw('id, "connection", queue, payload, "exception", failed_at');

        $data = datatables()->of($query)
            ->addColumn('formated_payload', function ($data) {
                return substr(strip_tags($data->payload), 0, 100) . (strlen($data->payload) > 100 ? ' ...' : '');
            })
            ->addColumn('formated_exception', function ($data) {
                return substr(strip_tags($data->exception), 0, 100) . (strlen($data->exception) > 100 ? ' ...' : '');
            })
            ->make(true);
        return $data;
    }
}
