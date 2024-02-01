<?php

namespace App\Http\Controllers\Api\Cms;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:cms permission-management view', ['only' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Permission::selectRaw('id, name, description, guard_name')
            ->with('roles:id,name,description')
            ->when(request('name'), function ($query) {
                $query->where('name', 'ILIKE', '%' . request('name') . '%')
                    ->orWhere('description', 'ILIKE', '%' . request('name') . '%');
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
            $query = Permission::selectRaw('id, name, description')
                ->with('roles:id,name,description')
                ->findOrFail($id);

            return ResponseFormat::ok($query);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }
}
