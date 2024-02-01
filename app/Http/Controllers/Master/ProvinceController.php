<?php

namespace App\Http\Controllers\Master;

use App\Helpers\ActivityLog;
use Cache;
use App\Models\Master\Location\Province;
use App\Helpers\ResponseFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\ProvinceRequest;
use App\Http\Requests\Master\WilayahRequest;
use App\Models\Master\Location\Country;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class ProvinceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:master province view',   ['only' => ['index']]);
        $this->middleware('permission:master province create', ['only' => ['create', 'store']]);
        $this->middleware('permission:master province update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:master province delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Provinsi';
        // END MANDATORY PARAMETER

        return view('master.location.province.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Tambah Provinsi';
        // END MANDATORY PARAMETER
        $data['country'] = Country::orderBy('name','asc')->get();

        return view('master.location.province.create', $data);
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
            'code' => 'nullable|unique:m_province,code',
            'country_id' => 'required',
            'name' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);
        try {
            // Save to database
            DB::beginTransaction();
            $province = new Province;
            $province->code = $request->code;
            $province->country_id = $request->country_id;
            $province->name = $request->name;
            $province->latitude = $request->latitude;
            $province->longitude = $request->longitude;
            $province->save();

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.province.index');
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
            $province = Province::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = $province->name;
            // END MANDATORY PARAMETER
            $data['province'] = $province;

            return view('master.location.province.edit', $data);
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
            'code' => 'required|unique:m_province,code,' . Hashids::decode($id)[0],
            'name' => 'required',
            'latitude' => 'numeric',
            'longitude' => 'numeric'
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $province = Province::findOrFail(Hashids::decode($id)[0]);
            $province->code = $request->code;
            $province->name = $request->name;
            $province->latitude = $request->latitude;
            $province->longitude = $request->longitude;
            $province->save();

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.province.edit', $id);
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
            $province = Province::findOrFail(Hashids::decode($id)[0]);
            $province->delete();

            // Send session flash message
            ActivityLog::delete($province, 'Delete province :subject.name');
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function getData(ProvinceRequest $request)
    {
        $query =  Province::select('id', 'country_id', 'name', 'latitude', 'longitude')
            ->when(request('country_id'), function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })
            ->when(request('id'), function ($query) use ($request) {
                $query->where('id', $request->id);
            })
            ->when(request('name'), function ($query) use ($request) {
                $query->where('name', 'ILIKE', '%' . $request->name . '%');
            })
            ->get();
        return ResponseFormat::ok($query);
    }

    public function getWilayah(WilayahRequest $request)
    {
        $query = Province::selectRaw('
                m_province.id as province_id, m_province.name as province,
                m_regency.id as regency_id, m_regency.name as regency,
                m_district.id as district_id, m_district.name as district,
                m_village.id as village_id, m_village.name as village
            ')
            ->join('m_regency', 'm_regency.province_id', '=', 'm_province.id')
            ->join('m_district', 'm_district.regency_id', '=', 'm_regency.id')
            ->join('m_village', 'm_village.district_id', '=', 'm_district.id')
            ->when(request('name'), function ($query) use ($request) {
                $query->where('m_province.name', 'ILIKE', '%' . $request->name . '%')
                    ->orWhere('m_regency.name', 'ILIKE', '%' . $request->name . '%')
                    ->orWhere('m_district.name', 'ILIKE', '%' . $request->name . '%')
                    ->orWhere('m_village.name', 'ILIKE', '%' . $request->name . '%');
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
        $query = Province::with('regencys');
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('diperbarui', function($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('regencys', function ($data) {
                $regencys = array();
                foreach ($data->regencys as $data) {
                    array_push($regencys, '<span class="label label-info" style="margin:1px">' . $data->name . '</span>');
                }
                return implode(" ", $regencys);
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('master province update')) {
                    array_push($action, '<a href="' . route('master.location.province.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('master province delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('master.location.province.destroy', Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['regencys', 'actions'])
            ->make(true);
        return $data;
    }
}
