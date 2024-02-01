<?php

namespace App\Http\Controllers\Master;

use App\Models\Master\Location\Village;
use App\Helpers\ResponseFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\VillageRequest;

class VillageController extends Controller
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
        $data['title'] = 'Kelurahan';
        // END MANDATORY PARAMETER

        return view('master.location.village.index', $data);
    }

    public function getData(VillageRequest $request)
    {
        $query = Village::select('id', 'district_id', 'name', 'latitude', 'longitude')
            ->when(request('id'), function ($query) use ($request) {
                $query->where('id', $request->id);
            })
            ->when(request('district_id'), function ($query) use ($request) {
                $query->where('district_id', $request->district_id);
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
        $query = Village::with('district', 'district.regency', 'district.regency.province');
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('diperbarui', function($data) {
                return $data->updated_at->diffForHumans();
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
            ->rawColumns(['actions'])
            ->make(true);
        return $data;
    }
}
