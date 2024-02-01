<?php

namespace App\Http\Controllers\Cms;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Vinkla\Hashids\Facades\Hashids;

class RoleManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms role-management view',   ['only' => ['index', 'show']]);
        $this->middleware('permission:cms role-management create', ['only' => ['create', 'store']]);
        $this->middleware('permission:cms role-management update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:cms role-management delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Roles';
        // END MANDATORY PARAMETER

        return view('cms.role.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Create Role';
        // END MANDATORY PARAMETER

        $data['permissions'] = Permission::selectRaw('id, name, description')
            ->orderBy('name')
            ->get()
            ->map(function ($permission) {
                $permission->module = explode(' ', $permission->name)[0];
                $permission->function = explode(' ', $permission->name)[1];
                $permission->display_name = explode(' ', $permission->name)[2];
                return $permission;
            })
            ->groupBy('module')
            ->map(function ($module, $name) {
                return (object) [
                    'id' => $name,
                    'module' => $name,
                    'function' => collect($module->all())
                        ->map(function ($permission) {
                            $permission->children = explode(' ', $permission->name)[1];
                            return $permission;
                        })
                        ->groupBy('children'),
                ];
            })
            ->values();

        return view('cms.role.create', $data);
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
            'name' => 'required|unique:cms_roles,name',
            'description' => 'required',
            'level' => 'required',
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $role = new Role;
            $role->name = $request->name;
            $role->description = $request->description;
            $role->level = $request->level;
            $role->save();

            // Save permission
            $permissions = $request->except(['_token', '_method', 'name', 'description', 'level']);
            $role_permissions = [];
            foreach ($permissions as $key => $value) {
                array_push($role_permissions, str_replace('_', ' ', $key));
            }
            $role->syncPermissions($role_permissions);

            DB::commit();
            // Send session flash message
            ActivityLog::create($role, 'Buat data Role dengan nama :subject.name');
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.role.index');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            ActivityLog::sentry($e);
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
            $role = Role::findOrFail($id);
            // MANDATORY PARAMETER
            $data['title'] = $role->name;
            // END MANDATORY PARAMETER

            $data['role'] = $role;
            $data['role_permission'] = collect($role->permissions)
                ->map(function ($value) {
                    return $value['name'];
                })->toArray();
            $data['permissions'] = Permission::selectRaw('id, name, description')
                ->orderBy('name')
                ->get()
                ->map(function ($permission) {
                    $permission->module = explode(' ', $permission->name)[0];
                    $permission->function = explode(' ', $permission->name)[1];
                    $permission->display_name = explode(' ', $permission->name)[2];
                    return $permission;
                })
                ->groupBy('module')
                ->map(function ($module, $name) {
                    return (object) [
                        'id' => $name,
                        'module' => $name,
                        'function' => collect($module->all())
                            ->map(function ($permission) {
                                $permission->children = explode(' ', $permission->name)[1];
                                return $permission;
                            })
                            ->groupBy('children'),
                    ];
                })
                ->values();

            return view('cms.role.show', $data);
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
            $role = Role::findOrFail($id);
            // MANDATORY PARAMETER
            $data['title'] = $role->name;
            // END MANDATORY PARAMETER

            $data['role'] = $role;
            $data['role_permission'] = collect($role->permissions)
                ->map(function ($value) {
                    return $value['name'];
                })->toArray();
            $data['permissions'] = Permission::select(['id', 'name', 'description'])
                ->orderBy('name')
                ->get()
                ->map(function ($permission) {
                    $permission->module = explode(' ', $permission->name)[0];
                    $permission->function = explode(' ', $permission->name)[1];
                    $permission->display_name = explode(' ', $permission->name)[2];
                    return $permission;
                })
                ->groupBy('module')
                ->map(function ($module, $name) {
                    return (object) [
                        'id' => $name,
                        'module' => $name,
                        'function' => collect($module->all())
                            ->map(function ($permission) {
                                $permission->children = explode(' ', $permission->name)[1];
                                return $permission;
                            })
                            ->groupBy('children'),
                    ];
                })
                ->values();

            return view('cms.role.edit', $data);
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
        // Validator
        $this->validate($request, [
            'name' => 'required|unique:cms_roles,name,' . $id . '',
            'description' => 'required',
            'level' => 'required',
        ]);
        try {
            // Save to database
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->description = $request->description;
            $role->level = $request->level;
            $role->save();

            // Save permission
            $permissions = $request->except(['_token', '_method', 'name', 'description', 'level']);
            $role_permissions = [];
            foreach ($permissions as $key => $value) {
                array_push($role_permissions, str_replace('_', ' ', $key));
            }
            $role->syncPermissions($role_permissions);

            DB::commit();
            // Send session flash message
            ActivityLog::update($role, 'Edit data Role dengan nama :subject.name');
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.role.show', Hashids::encode($role->id));
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
            $role = Role::findOrFail($id);
            if ($role->name == 'superadmin') {
                Alert::warning('Warning', 'Permission denied superadmin.')->autoclose(5000);
                return back();
            }
            $role->delete();

            // Send session flash message
            ActivityLog::delete($role, 'Hapus data Role dengan nama :subject.name');
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
        $query = Role::selectRaw('
                id, name, description, level
            ')
            ->with('permissions')
            ->withCount('users');

        $data = datatables()->of($query)
            ->addColumn('permissions', function ($data) {
                $permissions = [];
                $permissions_query = $data->permissions
                    ->sortBy('name')
                    ->map(function ($permission) {
                        $permission->module = explode(' ', $permission->name)[0];
                        $permission->function = explode(' ', $permission->name)[1];
                        $permission->display_name = explode(' ', $permission->name)[2];
                        return $permission;
                    })
                    ->groupBy('function')
                    ->map(function ($key, $value) {
                        return (object) [
                            'function' => $value,
                            'module' => $key->first()->module,
                        ];
                    })
                    ->values();
                foreach ($permissions_query as $permission) {
                    $permission_name = '[' . ucwords(str_replace('-', ' ', $permission->module)) . ']' . ' ' . $permission->function;
                    array_push($permissions, '<span class="label label-info" style="margin:1px">' . ucwords(str_replace('-', ' ', $permission_name)) . '</span>');
                }
                return implode("", $permissions);
            })
            ->addColumn('count', function ($data) {
                return '
                        <span class="label label-inverse" style="margin:1px">' . $data->users_count . ' Users</span>
                    ';
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('cms role-management view')) {
                    array_push($action, '<a href="' . route('cms.role.show', Hashids::encode($data->id)) . '" class="btn btn-outline-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>');
                }
                if (auth()->user()->can('cms role-management update')) {
                    array_push($action, '<a href="' . route('cms.role.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('cms role-management delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('cms.role.destroy', \Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['permissions', 'count', 'actions'])
            ->make(true);
        return $data;
    }
}
