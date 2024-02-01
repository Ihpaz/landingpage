<?php

namespace App\Http\Controllers\Api\Cms;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms role-management view', ['only' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Role::selectRaw('id, name, description, level, guard_name')
            ->with('permissions:id,name,description')
            ->when(request('name'), function ($query) {
                $query->where('name', 'ILIKE', '%' . request('name') . '%')
                    ->orWhere('description', 'ILIKE', '%' . request('name') . '%');
            })
            ->when(request('level'), function ($query) {
                $query->where('level', request('level'));
            })
            ->get();

        return ResponseFormat::ok($query);
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
            $query = Role::selectRaw('id, name, description')
                ->with('permissions:id,name,description')
                ->findOrFail($id);

            return ResponseFormat::ok($query);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }
}
