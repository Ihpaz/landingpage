<?php

namespace App\Http\Controllers\Cms;

use App\Exports\DatatablesQueryExport;
use App\Models\User;
use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Request as UserRequest;
use App\Jobs\UserImportJob;
use App\Models\Attachment;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Vinkla\Hashids\Facades\Hashids;

class UserManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms user-management view',   ['only' => ['index', 'show', 'exportExcel']]);
        $this->middleware('permission:cms user-management create', ['only' => ['create', 'store', 'importExcel']]);
        $this->middleware('permission:cms user-management update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:cms user-management delete', ['only' => ['destroy']]);
        $this->middleware('permission:cms user-management impersonate', ['only' => ['impersonate']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Users';
        // END MANDATORY PARAMETER

        $user = User::query();
        $data['total_users'] = Cache::remember('cms:user:total:users', config('cache.lifetime'), function () use ($user) {
            return $user->count();
        });
        $data['active_user'] = Cache::remember('cms:user:active:users', config('cache.lifetime'), function () use ($user) {
            return $user->where('status', 'ACTV')->count();
        });
        $data['new_users'] = Cache::remember('cms:user:new:users', config('cache.lifetime'), function () use ($user) {
            return $user->whereDate('created_at', '>', Carbon::now()->subDays(30))->count();
        });
        $data['users_status'] = Cache::remember('cms:user:status:users', config('cache.lifetime'), function () use ($user) {
            return collect($user->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')->get())
                ->pluck('total', 'status')->all();
        });

        // Filter
        $filter['users_role'] = Cache::remember('cms:users:role', config('cache.lifetime'), function () {
            return Role::selectRaw('id, name')->orderBy('name')->get();
        });
        $data['filter'] = $filter;

        return view('cms.user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Create User';
        // END MANDATORY PARAMETER

        $auth_level =  Role::select('level')->whereIn('name', auth()->user()->roles->pluck('name'))->min('level');
        $roles = Role::orderBy('name')->where('level', '>=', $auth_level)->get();
        if (!auth()->user()->hasRole('superadmin')) {
            $filtered = $roles->filter(function ($value, $key) {
                return $value['name'] != 'superadmin';
            });
            $roles = $filtered->values();
        }
        $data['roles'] = $roles;

        return view('cms.user.create', $data);
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
            'email' => 'required|email',
            'password' => 'required',
            'fullname' => 'required',
            'status' => 'required',
            'roles' => 'required'
        ]);
        try {
            $user = new User;
            $user->fullname = $request->fullname;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->nip = $request->nip;
            $user->phonenumber = $request->phonenumber;
            $user->company = $request->company;
            $user->department = $request->department;
            $user->position = $request->position;
            $user->status = 'ACTV';
            if ($request->has('thumbnail_photo')) {
                $image = $request->file('thumbnail_photo');
                $filename = $image->getClientOriginalName();
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $user->thumbnail_photo = base64_encode($image_resize->stream());
            }
            $user->syncRoles($request->roles);
            $user->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.user.show', Hashids::encode($user->id));
        } catch (\Illuminate\Database\QueryException $e) {
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
            $user = User::findOrFail($id);
            // MANDATORY PARAMETER
            $data['title'] = $user->fullname;
            // END MANDATORY PARAMETER

            $data['user'] = $user;

            return view('cms.user.show', $data);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
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
            $user = User::findOrFail($id);
            // MANDATORY PARAMETER
            $data['title'] = $user->fullname;
            // END MANDATORY PARAMETER

            $data['user'] = $user;
            $user_level = Role::select('level')->whereIn('name', $user->roles->pluck('name'))->min('level') ?? 9;
            $auth_level =  Role::select('level')->whereIn('name', auth()->user()->roles->pluck('name'))->min('level');

            if ($user_level < $auth_level) {
                Alert::warning('Warning', "Permission denied, you don't have an authorization to edit this user.")->autoclose(false);
                return back();
            }

            $roles = Role::orderBy('name')->where('level', '>=', $auth_level)->get();
            if (!auth()->user()->hasRole('superadmin')) {
                $filtered = $roles->filter(function ($value, $key) {
                    return $value['name'] != 'superadmin';
                });
                $roles = $filtered->values();
            }
            $data['roles'] = $roles;

            return view('cms.user.edit', $data);
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
            'fullname' => 'required',
            'status' => 'required',
            'thumbnail_photo' => 'image'
        ]);
        try {
            $user = User::findOrFail($id);
            $user->fullname = $request->fullname;
            $user->nickname = $request->nickname;
            $user->phonenumber = $request->phonenumber;
            $user->company = $request->company;
            $user->department = $request->department;
            $user->nip = $request->nip;
            $user->pernr = $request->pernr;
            $user->position = $request->position;
            $user->status = $request->status;
            if ($request->input('password')) {
                $user->password = bcrypt($request->password);
            }
            if ($request->input('thumbnail_photo')) {
                $image = $request->file('thumbnail_photo');
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $user->thumbnail_photo = base64_encode($image_resize->stream());
            }
            $old_roles = $user->roles->pluck('name');
            $new_roles = collect($request->roles);
            $diff_old_roles = $old_roles->diff($new_roles);
            $diff_new_roles = $new_roles->diff($old_roles);

            if ($diff_new_roles->count()) {
                $message = $user->fullname . ' diberikan role sebagai [' . $diff_new_roles->implode(', ') . '] oleh ' . auth()->user()->fullname;
                ActivityLog::logCauserPerformed('Roles', $message, $user, $user);
            }
            if ($diff_old_roles->count()) {
                $message = $user->fullname . ' dihapus sebagai role [' . $diff_old_roles->implode(', ') . '] oleh ' . auth()->user()->fullname;
                ActivityLog::logCauserPerformed('Roles', $message, $user, $user);
            }
            $user->syncRoles($request->roles);

            // Send message if user is ACTIVATE
            $user->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil diperbarui')->autoclose(5000);
            return redirect()
                ->route('cms.user.show', Hashids::encode($user->id));
        } catch (\Illuminate\Database\QueryException $e) {
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
            $user = User::findOrFail($id);
            if ($user->id == auth()->user()->id) {
                Alert::warning('Warning', 'Permission denied remove own user.')->autoclose(5000);
                return back();
            }
            if ($user->hasRole('superadmin')) {
                Alert::error('Warning', 'Permission denied remove superadmin user.')->autoclose(5000);
                return back();
            }
            $user->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function impersonate($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->hasRole('superadmin')) {
                Alert::error('Warning', 'Impersonate role superadmin ditolak')->autoclose(5000);
                return back();
            }
            ActivityLog::impersonate($user->fullname);
            // Send session flash message
            auth()->user()->impersonate($user);
            Alert::success(trans('common.success'), 'Mengakses sebagai akun ' . $user->fullname)->autoclose(5000);

            return redirect()
                ->route('backend.dashboard.index');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function leaveImpersonate()
    {
        $impersonateUser = auth()->user()->fullname;
        auth()->user()->leaveImpersonation();

        activity('Impersonate')
            ->log(':causer.fullname leave impersonate user ' . $impersonateUser . '');

        if (auth()->user()->can('cms user-management view')) {
            return redirect()->route('cms.user.index');
        }
        return redirect()
            ->route('backend.dashboard.index');
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dokumen' => 'required|file|max:25000',
        ]);
        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator);
        }

        try {
            if ($request->hasFile('dokumen')) {
                $file = $request->file('file');
                $filename = 'user-' . time() . '.' . $request->dokumen->getClientOriginalExtension();
                Storage::disk('temp')->putFileAs('import', $request->file('dokumen'), $filename);
                UserImportJob::dispatch(auth()->user(), $filename);
            }

            Alert::success(trans('common.success'), 'Import data user telah berhasil')->autoclose(5000);
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
        $query = User::selectRaw('
                id, fullname, email, company, department, position, thumbnail_photo, avatar_url, status, nip
            ')
            ->with('roles:id,name,description')
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('role'), function ($query) {
                $query->role(request('role'));
            });

        $data = datatables()->of($query)
            ->addColumn('display_name', function ($data) {
                return '
                        <img alt="image" class="rounded-circle" src="' . $data->user_thumbnail . '" style="width:30px;height:30px;">
                        ' . $data->fullname . '
                    ';
            })
            ->addColumn('role', function ($data) {
                $roles = array();
                foreach ($data->getRoleNames() as $role) {
                    array_push($roles, '<span class="label label-info mb-0">' . strtoupper($role) . '</span>');
                }
                return implode(" ", $roles);
            })
            ->addColumn('status', function ($data) {
                $online = $data->is_online ? 'info' : 'inverse';
                if ($data->status == 'ACTV') {
                    return '<span class="label label-' . $online . ' mb-0">Active</span>';
                }
                return ' <span class="label label-' . $online . ' mb-0">In Active</span>';
            })
            ->addColumn('formated_created_at', function ($data) {
                return $data->formated_created_at;
            })
            ->addColumn('formated_updated_at', function ($data) {
                return $data->formated_updated_at;
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                if (auth()->user()->can('cms user-management impersonate')) {
                    array_push($action, '<a href="' . route('cms.user.impersonate', Hashids::encode($data->id)) . '" class="btn btn-secondary btn-xs" title="Impersonate"><i class="fa fa-user-circle-o"></i></a>');
                }
                if (auth()->user()->can('cms user-management view')) {
                    array_push($action, '<a href="' . route('cms.user.show', Hashids::encode($data->id)) . '" class="btn btn-outline-info btn-xs" title="Detail"><i class="fa fa-eye"></i></a>');
                }
                if (auth()->user()->can('cms user-management update')) {
                    array_push($action, '<a href="' . route('cms.user.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                }
                if (auth()->user()->can('cms user-management delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->email . '\',\'' . route('cms.user.destroy', \Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['display_name', 'role', 'status', 'actions']);

        if (request('action') == 'excel') {
            $exportHeader = [
                ['title' => 'Nama', 'name' => 'fullname'],
                ['title' => 'Email', 'name' => 'email'],
                ['title' => 'NIP', 'name' => 'nip'],
                ['title' => 'NIK', 'name' => 'nik'],
                ['title' => 'Company', 'name' => 'company'],
                ['title' => 'Department', 'name' => 'department'],
                ['title' => 'Title', 'name' => 'position'],
                ['title' => 'Status', 'name' => 'status']
            ];
            $exportFilename = 'ExportUser ' . Carbon::now()->format('Ymd') . '.xlsx';

            ActivityLog::logWithProperty('Export', 'Export ' . $exportFilename, $request);
            return Excel::download(new DatatablesQueryExport($query, $exportHeader), $exportFilename);
        }
        return $data->make(true);
    }
}
