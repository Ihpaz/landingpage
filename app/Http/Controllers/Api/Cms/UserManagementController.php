<?php

namespace App\Http\Controllers\Api\Cms;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Image;

class UserManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms user-management view', ['only' => ['index', 'show']]);
        $this->middleware('permission:cms user-management create', ['only' => ['store']]);
        $this->middleware('permission:cms user-management update', ['only' => ['update']]);
        $this->middleware('permission:cms user-management delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserRequest $request)
    {
        $query = User::selectRaw('
                id, guid, email,
                fullname, nickname,
                nip, nik, pernr,
                company, department, position,
                status
            ')
            ->when(request('name'), function ($query) {
                $query->where('fullname', 'ILIKE', '%' . request('name') . '%')
                    ->orWhere('nickname', 'ILIKE', '%' . request('name') . '%');
            })
            ->when(request('company'), function ($query) {
                $query->where('company', 'ILIKE', '%' . request('company') . '%');
            })
            ->when(request('department'), function ($query) {
                $query->where('department', 'ILIKE', '%' . request('department') . '%');
            })
            ->when(request('position'), function ($query) {
                $query->where('position', 'ILIKE', '%' . request('position') . '%');
            })
            ->with('roles:id,name,description')
            ->orderBy('fullname')
            ->paginate();

        return ResponseFormat::ok($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = new User;
            $user->email = $request->email;
            $user->fullname = $request->fullname;
            $user->nickname = $request->nickname;
            $user->phonenumber = $request->phonenumber;
            $user->company = $request->company;
            $user->department = $request->department;
            $user->nip = $request->nip;
            $user->nik = $request->nik;
            $user->pernr = $request->pernr;
            $user->position = $request->position;
            $user->status = $request->status;
            $user->password = bcrypt($request->password);
            if ($request->has('thumbnail_photo')) {
                $image = $request->file('thumbnail_photo');
                $filename = $image->getClientOriginalName();
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $user->thumbnail_photo = base64_encode($image_resize->stream());
            }
            $user->save();

            return ResponseFormat::ok('Data berhasil disimpan');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
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
            $query = User::selectRaw('
                    id, guid, email,
                    fullname, nickname,
                    nip, nik, pernr,
                    company, department, position,
                    status
                ')
                ->with('roles:id,name,description')
                ->orderBy('fullname')
                ->findOrFail($id);

            return ResponseFormat::ok($query);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->fullname = $request->fullname;
            $user->nickname = $request->nickname;
            $user->phonenumber = $request->phonenumber;
            $user->company = $request->company;
            $user->department = $request->department;
            $user->nip = $request->nip;
            $user->nik = $request->nik;
            $user->pernr = $request->pernr;
            $user->position = $request->position;
            $user->status = $request->status;
            $user->save();

            return ResponseFormat::ok('Data berhasil disimpan');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
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
            $user->delete();
            
            return ResponseFormat::ok('Data berhasil dihapus');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }
}
