<?php

namespace App\Http\Controllers\Master;

use App\Helpers\ActivityLog;
use Cache;
use App\Models\Master\Location\Regency;
use App\Helpers\ResponseFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\RegencyRequest;
use App\Models\Master\Location\Province;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class RegencyController extends Controller
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
        $data['title'] = 'Kota';
        // END MANDATORY PARAMETER

        return view('master.location.regency.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Kota';
        // END MANDATORY PARAMETER

        $data['province'] = Province::orderBy('name','asc')->get();

        return view('master.location.regency.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|unique:m_regency,code',
            'province_id' => 'required',
            'name' => 'required',
            'latitude' => 'numeric',
            'longitude' => 'numeric'
        ]);
        try {
            // Save to database
            DB::beginTransaction();
            $regency = new Regency;
            $regency->province_id = $request->province_id;
            $regency->code = $request->code;
            $regency->latitude = $request->latitude;
            $regency->longitude = $request->longitude;
            $regency->save();

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.regency.index');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $regency = Regency::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = $regency->name;
            // END MANDATORY PARAMETER

            $data['regency'] = $regency;

            return view('master.location.regency.edit', $data);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required|unique:m_regency,code,'.Hashids::decode($id)[0],
            'province_id' => 'required',
            'name' => 'required',
            'latitude' => 'numeric',
            'longitude' => 'numeric'
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $regency = Regency::findOrFail(Hashids::decode($id)[0]);
            $regency->province_id = $request->province_id;
            $regency->code = $request->code;
            $regency->latitude = $request->latitude;
            $regency->longitude = $request->longitude;
            $regency->save();

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.regency.edit', $id);
        } catch (\Exception $e) {
            DB::rollBack();
            ActivityLog::sentry($e);
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
    public function destroy($id)
    {
        try {
            $regency = Regency::findOrFail(Hashids::decode($id)[0]);
            $regency->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function getData(RegencyRequest $request)
    {
        $query =  Regency::select('id', 'province_id', 'name', 'latitude', 'longitude')
            ->when(request('id'), function ($query) use ($request) {
                $query->where('id', $request->id);
            })
            ->when(request('province_id'), function ($query) use ($request) {
                $query->where('province_id', $request->province_id);
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
        $query = Regency::with('province', 'districts');
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('diperbarui', function($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('districts', function ($data) {
                $districts = array();
                foreach ($data->districts as $data) {
                    array_push($districts, '<span class="label label-info" style="margin:1px">' . $data->name . '</span>');
                }
                return implode(" ", $districts);
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('master regency update')) {
                    array_push($action, '<a href="' . route('master.location.regency.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('master regency delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('master.location.regency.destroy', Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['districts', 'actions'])
            ->make(true);
        return $data;
    }
}
