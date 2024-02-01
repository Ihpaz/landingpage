<?php

namespace App\Http\Controllers\Master;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\Master\Location\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class CurrencyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:master currency view',   ['only' => ['index']]);
        $this->middleware('permission:master currency create', ['only' => ['create', 'store']]);
        $this->middleware('permission:master currency update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:master currency delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Mata Uang';
        // END MANDATORY PARAMETER

        return view('master.location.currency.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Tambah Mata Uang';
        // END MANDATORY PARAMETER

        return view('master.location.currency.create', $data);
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
            'code' => 'required|unique:m_currency,code',
            'name' => 'required',
            'symbol' => 'required',
        ]);
        try {
            // Save to database
            DB::beginTransaction();
            $currency = new Currency;
            $currency->code = $request->code;
            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->save();

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.currency.index');
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
            $currency = currency::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = $currency->name;
            // END MANDATORY PARAMETER

            $data['currency'] = $currency;

            return view('master.location.currency.edit', $data);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors(trans('exceptions.generic'));
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
            'code' => 'required|unique:m_currency,code,'.Hashids::decode($id)[0],
            'name' => 'required',
            'symbol' => 'required',
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $currency = Currency::findOrFail(Hashids::decode($id)[0]);
            $currency->code = $request->code;
            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->save();

            DB::commit();
            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('master.location.currency.edit', $id);
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
            $currency = Currency::findOrFail(Hashids::decode($id)[0]);
            $currency->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $query = Currency::query();
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('diperbarui', function($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('master currency update')) {
                    array_push($action, '<a href="' . route('master.location.currency.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('master currency delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('master.location.currency.destroy', Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['actions'])
            ->make(true);
        return $data;
    }
}
