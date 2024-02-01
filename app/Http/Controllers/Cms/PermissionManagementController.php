<?php

namespace App\Http\Controllers\Cms;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Vinkla\Hashids\Facades\Hashids;

class PermissionManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms permission-management view',   ['only' => ['index']]);
        $this->middleware('permission:cms permission-management create', ['only' => ['create', 'store']]);
        $this->middleware('permission:cms permission-management update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:cms permission-management delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Permissions';
        // END MANDATORY PARAMETER

        return view('cms.permission.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Create Permission';
        // END MANDATORY PARAMETER

        return view('cms.permission.create', $data);
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
            'action' => 'required',
            'module' => 'required',
            'function' => 'required',
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $permission = new Permission;
            $permission->name = str_slug($request->module) . ' ' . str_slug($request->function) . ' ' . str_slug($request->action);
            $permission->description = $request->description;
            $permission->guard_name = 'web';
            $permission->save();

            DB::commit();
            // Send session flash message
            ActivityLog::create($permission, 'Buat data Permission dengan nama :subject.name');
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.permission.index');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
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
            $permission = Permission::findOrFail($id);
            // MANDATORY PARAMETER
            $data['title'] = ucwords(str_replace('-', ' ', $permission->name));
            // END MANDATORY PARAMETER

            $permission->module = ucwords(str_replace('-', ' ', explode(' ', $permission->name)[0]));
            $permission->function = ucwords(str_replace('-', ' ', explode(' ', $permission->name)[1]));
            $permission->action = explode(' ', $permission->name)[2] ?? null;
            $data['permission'] = $permission;

            return view('cms.permission.show', $data);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors(trans('exceptions.generic'));
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
            $permission = Permission::findOrFail($id);
            // MANDATORY PARAMETER
            $data['title'] = ucwords(str_replace('-', ' ', $permission->name));
            // END MANDATORY PARAMETER

            $permission->module = ucwords(str_replace('-', ' ', explode(' ', $permission->name)[0]));
            $permission->function = ucwords(str_replace('-', ' ', explode(' ', $permission->name)[1]));
            $permission->action = explode(' ', $permission->name)[2] ?? null;
            $data['permission'] = $permission;

            return view('cms.permission.edit', $data);
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
            'action' => 'required',
            'module' => 'required',
            'function' => 'required',
            'description' => 'required',
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $permission = Permission::findOrFail($id);
            $permission->name = str_slug($request->module) . ' ' . str_slug($request->function) . ' ' . str_slug($request->action);
            $permission->description = $request->description;
            $permission->save();

            DB::commit();
            // Send session flash message
            ActivityLog::update($permission, 'Edit data Permission dengan nama :subject.name');
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.permission.show', Hashids::encode($permission->id));
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
            $permission = Permission::findOrFail($id);
            $permission->delete();

            // Send session flash message
            ActivityLog::delete($permission, 'Hapus data Permission dengan nama :subject.name');
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
        $query = Permission::select();
        $data = datatables()->of($query)
            ->addColumn('module', function ($data) {
                return ucwords(str_replace('-', ' ', explode(' ', $data->name)[0]));
            })
            ->addColumn('function', function ($data) {
                return ucwords(str_replace('-', ' ', explode(' ', $data->name)[1]));
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('cms permission-management view')) {
                    array_push($action, '<a href="' . route('cms.permission.show', Hashids::encode($data->id)) . '" class="btn btn-outline-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>');
                }
                if (auth()->user()->can('cms permission-management update')) {
                    array_push($action, '<a href="' . route('cms.permission.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('cms permission-management delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('cms.permission.destroy', \Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['module', 'function', 'actions'])
            ->make(true);
        return $data;
    }
}
