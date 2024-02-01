<?php

namespace App\Http\Controllers\Master;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CountryRequest;
use App\Models\Master\Location\Country;
use App\Models\Master\Location\Currency;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class CountryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:master country view',   ['only' => ['index']]);
        $this->middleware('permission:master country create', ['only' => ['create', 'store']]);
        $this->middleware('permission:master country update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:master country delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Negara';
        // END MANDATORY PARAMETER

        return view('master.location.country.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Tambah Negara';
        // END MANDATORY PARAMETER
        $data['currency'] = Currency::orderBy('code')->get();

        return view('master.location.country.create', $data);
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
            'code' => 'required|unique:m_country,code',
            'name' => 'required',
            'alpha_2' => 'required|unique:m_country,alpha_2',
            'alpha_3' => 'required|unique:m_country,alpha_3',
            'language' => 'required',
            'currency' => 'required',
            'latitude' => 'numeric',
            'longitude' => 'numeric'
        ]);
        try {
            // Save to database
            DB::beginTransaction();
            $country = new Country;
            $country->code = $request->code;
            $country->name = $request->name;
            $country->alpha_2 = $request->alpha_2;
            $country->alpha_3 = $request->alpha_3;
            $country->language = $request->language;
            $country->latitude = $request->latitude;
            $country->longitude = $request->longitude;
            $country->save();

            $country->currencies()->sync($request->currency);

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.country.index');
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
            $country = Country::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = $country->name;
            // END MANDATORY PARAMETER

            $data['country'] = $country;
            $data['currency'] = Currency::orderBy('code')->get();

            return view('master.location.country.edit', $data);
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
            'name' => 'required',
            'alpha_2' => 'required|unique:m_country,alpha_2,' . Hashids::decode($id)[0],
            'alpha_3' => 'required|unique:m_country,alpha_3,' . Hashids::decode($id)[0],
            'language' => 'required',
            'currency' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $country = Country::findOrFail(Hashids::decode($id)[0]);
            $country->name = $request->name;
            $country->alpha_2 = $request->alpha_2;
            $country->alpha_3 = $request->alpha_3;
            $country->latitude = $request->latitude;
            $country->longitude = $request->longitude;
            $country->save();

            $country->currencies()->sync($request->currency);

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.country.edit', $id);
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
            $country = Country::findOrFail(Hashids::decode($id)[0]);
            $country->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function getData(CountryRequest $request)
    {
        $query = Country::select('id', 'name', 'alpha_2', 'alpha_3', 'latitude', 'longitude')
            ->when(request('id'), function ($query) use ($request) {
                $query->where('id', $request->id);
            })
            ->when(request('code'), function ($query) use ($request) {
                $query->where('alpha_2', $request->code)
                    ->orWhere('alpha_3', $request->code);
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
        $query = Country::with('provincies', 'currencies');
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('currencies', function ($data) {
                return implode(', ', $data->currencies->pluck('code')->toArray());
            })
            ->addColumn('diperbarui', function ($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('master country update')) {
                    array_push($action, '<a href="' . route('master.location.country.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('master country delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('master.location.country.destroy', Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['currencies', 'actions'])
            ->make(true);
        return $data;
    }
}
