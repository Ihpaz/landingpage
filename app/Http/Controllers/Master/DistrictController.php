<?php

namespace App\Http\Controllers\Master;

use App\Helpers\ActivityLog;
use App\Models\Master\Location\District;
use App\Helpers\ResponseFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\DistrictRequest;
use Illuminate\Support\Facades\Cache;

class DistrictController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadmin', ['only' => ['index', 'datatable']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Kecamatan';
        // END MANDATORY PARAMETER

        return view('master.location.district.index', $data);
    }

    public function getData(DistrictRequest $request)
    {
        $query = District::select('id', 'regency_id', 'name', 'latitude', 'longitude')
            ->when(request('id'), function ($query) use ($request) {
                $query->where('id', $request->id);
            })
            ->when(request('regency_id'), function ($query) use ($request) {
                $query->where('regency_id', $request->regency_id);
            })
            ->when(request('name'), function ($query) use ($request) {
                $query->where('name', 'ILIKE', '%' . $request->name . '%');
            })
            ->get();
        return ResponseFormat::ok($query);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $query = District::with('regency', 'regency.province', 'villages');
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('diperbarui', function($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('villages', function ($data) {
                $villages = array();
                foreach ($data->villages as $data) {
                    array_push($villages, '<span class="label label-info" style="margin:1px">' . $data->name . '</span>');
                }
                return implode(" ", $villages);
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('master village update')) {
                    array_push($action, '<a href="' . route('master.location.village.edit', $data->id) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('master village delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('master.location.village.destroy', $data->id) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['villages', 'actions'])
            ->make(true);
        return $data;
    }
}
