<?php

namespace App\Http\Controllers\Cms;

use Cache;
use App\Models\User;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Vinkla\Hashids\Facades\Hashids;

class ActivityLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms activity-log view', ['only' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Activity Log';
        // END MANDATORY PARAMETER
        $filter['type'] = Activity::selectRaw('DISTINCT log_name')->orderBy('log_name')->get();
        $filter['model'] = Activity::selectRaw('DISTINCT subject_type')->whereNotNull('subject_type')->orderBy('subject_type')->get();
        $data['filter'] = $filter;

        return view('cms.activity.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // MANDATORY PARAMETER
            $data['title'] = 'Log ' . $id;
            // END MANDATORY PARAMETER

            $activity = Activity::with('causer')->findOrFail($id);
            $activity->changes();

            $data['activity'] = $activity;
            $data['logname'] = $this->createTypeColumn($activity);

            return view('cms.activity.show', $data);
        } catch (\Exception $e) {
            return back()
                ->withErrors(trans('exceptions.generic'));
        }
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $query = Activity::with('causer:id,fullname')
            ->when(request('log_name'), function ($query) {
                $query->where('log_name', request('log_name'));
            }, function ($query) {
                $query->whereNotIn('log_name', ['Login', 'Logout', 'Console']);
            })
            ->when(request('causer_id'), function ($query) {
                $query->where('causer_id', request('causer_id'));
            })
            ->when(request('causer_name'), function ($query) {
                $user_list = User::select('id')->where('fullname', 'ILIKE', '%' . request('causer_name') . '%')->pluck('id');
                $query->whereIn('causer_id', $user_list);
            })
            ->when(request('model'), function ($query) {
                $query->where('subject_type', request('model'));
            })
            ->when(request('date_start') && request('date_end'), function ($query) {
                $query->whereBetween(
                    'created_at',
                    [
                        Carbon::createFromFormat('d/m/Y', request('date_start')),
                        Carbon::createFromFormat('d/m/Y', request('date_end'))
                    ]
                );
            });

        $data = datatables()->of($query)
            ->addColumn('time', function ($data) {
                return $data->created_at . '
                        <p class="m-0"><small style="white-space: normal">' . $data->created_at->diffForHumans() . '</small></p>  
                    ';
            })
            ->addColumn('user', function ($data) {
                $causer = $data->causer ? $data->causer->email : '<span class="label label-warning">System</span>';
                $impersonate = !empty($data->properties['impersonate']) ? '<span class="label label-danger"><i class="fa fa-exclamation-triangle"></i></span>' : null;

                return $impersonate ? $impersonate . ' ' . $causer : $causer;
            })
            ->addColumn('type', function ($data) {
                return $this->createTypeColumn($data);
            })
            ->addColumn('ip', function ($data) {
                return $data->getExtraProperty('ipaddress');
            })
            ->addColumn('formated_description', function ($data) {
                return '
                        <strong>' . class_basename($data->subject_type) . '</strong>
                        <p class="m-0"><small>' . $data->description . '</small></p>  
                    ';
            })
            ->addColumn('actions', function ($data) {
                if ($data->getExtraProperty('url')) {
                    return '
                            <a href="' . route('cms.activity.show', Hashids::encode($data->id)) . '" class="btn btn-outline-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                            <a href="' . $data->getExtraProperty('url') . '" class="btn btn-outline-primary btn-xs" title="Redirect"><i class="fa fa-mail-reply"></i></a>
                        ';
                }
                return '
                        <a href="' . route('cms.activity.show', Hashids::encode($data->id)) . '" class="btn btn-outline-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                        <button class="btn btn-outline-default btn-xs" title="Can\'t Redirect" disabled><i class="fa fa-mail-reply"></i></button>
                    ';
            })
            ->rawColumns(['time', 'user', 'type', 'ip', 'formated_description', 'actions'])
            ->make(true);
        return $data;
    }


    /**
     * Create actions column for datatables ajax request
     *
     * @return string
     */
    protected function createTypeColumn($data)
    {
        $type = $data->log_name;
        $label = 'label-inverse';

        switch ($type) {
            case 'Create':
                $label = 'label-success';
                break;
            case 'Update':
                $label = 'label-warning';
                break;
            case 'Delete':
                $label = 'label-danger';
                break;
            case 'Login':
                $label = 'label-info';
                break;
            case 'Logout':
                $label = 'label-info';
                break;
            case 'Info':
                $label = 'label-info';
                break;
            case 'Schedule':
                $label = 'label-warning';
                break;
            default:
                break;
        }
        return '
            <span class="label ' . $label . '">' . $type . '</span>      
        ';
    }
}
