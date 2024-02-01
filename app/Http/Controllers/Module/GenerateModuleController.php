<?php

namespace App\Http\Controllers\Module;

use App\Exports\DatatablesQueryExport;
use App\Helpers\ActivityLog;
use App\Helpers\ModuleRules;
use App\Http\Controllers\Controller;
use App\Models\Menu\ModuleField;
use App\Models\Menu\Modules;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class GenerateModuleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $slug = Route::current()->parameter('slug');

        $this->middleware('permission:module ' . $slug . ' view',   ['only' => ['index', 'show', 'exportExcel']]);
        $this->middleware('permission:module ' . $slug . ' create', ['only' => ['create', 'store']]);
        $this->middleware('permission:module ' . $slug . ' update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:module ' . $slug . ' delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        $module = Modules::where('slug', $slug)
            ->with('fields')
            ->firstOrFail();

        // MANDATORY PARAMETER
        $data['title'] = $module->name;
        // END MANDATORY PARAMETER
        $data['module'] = $module;

        return view('module.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($slug)
    {
        $module = Modules::where('slug', $slug)
            ->with('fields')
            ->firstOrFail();

        // MANDATORY PARAMETER
        $data['title'] = $module->name;
        // END MANDATORY PARAMETER
        $data['module'] = $module;

        return view('module.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $slug)
    {
        $module = Modules::where('slug', $slug)
            ->with('fields')
            ->firstOrFail();
        $rules = ModuleRules::validate($module, $request);
        $this->validate($request, $rules);

        try {
            $model = str_replace("/", "\\", $module->full_path_model);
            $query = new $model;
            foreach ($module->fields as $key) {
                $query->{$key->colname} = $request->{$key->colname};
            }
            $query->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('module.show', ['slug' => $slug, 'id' => Hashids::encode($query->id)]);
        } catch (\Illuminate\Database\QueryException $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug, string $id)
    {
        $module = Modules::where('slug', $slug)
            ->with('fields')
            ->firstOrFail();

        $model = str_replace("/", "\\", $module->full_path_model);
        $query = $model::findOrFail(Hashids::decode($id)[0]);

        // MANDATORY PARAMETER
        $data['title'] = $module->name;
        // END MANDATORY PARAMETER
        $data['module'] = $module;
        $data['field'] = $query;

        return view('module.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $id)
    {
        $module = Modules::where('slug', $slug)
            ->with('fields')
            ->firstOrFail();

        $model = str_replace("/", "\\", $module->full_path_model);
        $query = $model::findOrFail(Hashids::decode($id)[0]);

        // MANDATORY PARAMETER
        $data['title'] = $module->name;
        // END MANDATORY PARAMETER
        $data['module'] = $module;
        $data['field'] = $query;

        return view('module.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $id)
    {
        $module = Modules::where('slug', $slug)
            ->with('fields')
            ->firstOrFail();
        $rules = ModuleRules::validate($module, $request, true);
        $this->validate($request, $rules);

        try {
            $model = str_replace("/", "\\", $module->full_path_model);
            $query = $model::findOrFail(Hashids::decode($id)[0]);
            foreach ($module->fields as $key) {
                $query->{$key->colname} = $request->{$key->colname};
            }
            $query->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return back();
        } catch (\Illuminate\Database\QueryException $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug, string $id)
    {
        try {
            $module = Modules::where('slug', $slug)
                ->with('fields')
                ->firstOrFail();
            $model = str_replace("/", "\\", $module->full_path_model);
            $query = $model::findOrFail(Hashids::decode($id)[0]);
            $query->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function datatable(Request $request, string $slug)
    {
        $module = Modules::where('slug', $slug)
            ->with('fields')
            ->firstOrFail();

        $model = str_replace("/", "\\", $module->full_path_model);
        $query = $model::select();
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($data) use ($slug) {
                $action = array();
                if (auth()->user()->can('module ' . $slug . ' view')) {
                    array_push($action, '<button class="btn btn-outline-info btn-xs" data-toggle="modal" onclick="showModalDetailLg(\'' . route('module.show', ['slug' => $slug, 'id' => Hashids::encode($data->id)]) . '\')" title="' . trans('common.show') . '"><i class="fa fa-eye"></i></button>');
                }
                if (auth()->user()->can('module ' . $slug . ' update')) {
                    array_push($action, '<button class="btn btn-outline-warning btn-xs" data-toggle="modal" onclick="showModalEditLg(\'' . route('module.edit', ['slug' => $slug, 'id' => Hashids::encode($data->id)]) . '\')" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></button>');
                }
                if (auth()->user()->can('module ' . $slug . ' delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->id . '\',\'' . route('module.destroy', ['slug' => $slug, 'id' => Hashids::encode($data->id)]) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['actions']);

        if (request('action') == 'excel') {
            $exportHeader = collect($module->fields)->map(function($item) {
                return ['title' => $item->label, 'name' => $item->colname];
            })->toArray();
            $exportFilename = 'Export_ '. $slug .'.' . Carbon::now()->format('Ymd') . '.xlsx';

            ActivityLog::logWithProperty('Export', 'Export ' . $exportFilename, $request);
            return Excel::download(new DatatablesQueryExport($query, $exportHeader), $exportFilename);
        }
        return $data->make(true);
    }
}
