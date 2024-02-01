<?php

namespace App\Http\Controllers\Cms;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use App\Models\Menu\Modules;
use App\Utils\Materialdesign;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Vinkla\Hashids\Facades\Hashids;

class MenuManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Menu Management';
        // END MANDATORY PARAMETER
        $data['menus'] = Menu::with('children','children.children','children.children.children')
            ->where('parent', 0)
            ->orderBy('hierarchy', 'asc')
            ->get();
        $data['module'] = Modules::orderBy('name')->get();
        $data['mdi'] = Materialdesign::getIcons();
        $data['permission'] = Permission::select(['id', 'name'])->orderBy('name')->get();

        return view('cms.menu.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $menu = new Menu;
            $menu->name = $request->name;
            $menu->type = $request->type;
            if ($request->type != 'separator') {
                $menu->url = $request->url;
                $menu->icon = $request->icon;
            } else {
                $menu->url = 'javascript:void(0)';
            }
            if ($request->type == 'module') {
                $menu->url = $request->module;
            }
            $menu->permission = $request->permission;
            $menu->parent = 0;
            $menu->hierarchy = Menu::where('parent', 0)->count() + 1;
            $menu->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $menu = Menu::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = $menu->name;
            // END MANDATORY PARAMETER
            $data['menu'] = $menu;
            $data['mdi'] = Materialdesign::getIcons();
            $data['module'] = Modules::orderBy('name')->get();
            $data['permission'] = Permission::select(['id', 'name'])->orderBy('name')->get();

            return view('cms.menu.edit', $data);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $menu = Menu::findOrFail(Hashids::decode($id)[0]);
            $menu->name = $request->name;
            if ($request->type != 'separator') {
                $menu->url = $request->url;
                $menu->icon = $request->icon;
            } else {
                $menu->url = 'javascript:void(0)';
            }
            $menu->permission = $request->permission;
            $menu->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.menu.index');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        $json_menu = json_decode($request->menus, true);
        $order = 1;
        // Parent
        foreach ($json_menu as $menu) {
            $pid = $menu['id'];
            $pmenu = Menu::findOrFail($pid);
            $pmenu->parent = 0;
            $pmenu->hierarchy = $order;
            $pmenu->disableLogging();
            $pmenu->save();

            if (isset($menu['children'])) {
                $corder = 1;
                // Child
                foreach ($menu['children'] as $child) {
                    $cid = $child['id'];
                    $cmenu = Menu::findOrFail($cid);
                    $cmenu->parent = $pid;
                    $cmenu->hierarchy = $corder;
                    $cmenu->disableLogging();
                    $cmenu->save();
                    $corder++;

                    // Grand Child
                    if (isset($child['children'])) {
                        $gorder = 1;
                        foreach ($child['children'] as $grand) {
                            $gid = $grand['id'];
                            $gmenu = Menu::findOrFail($gid);
                            $gmenu->parent = $cid;
                            $gmenu->hierarchy = $gorder;
                            $gmenu->disableLogging();
                            $gmenu->save();
                            $gorder++;
                        }
                    }
                }
            }
            $order++;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $menu = Menu::findOrFail(Hashids::decode($id)[0]);
            $parent = Menu::where('parent', $menu->id)
                ->where('parent', '!=', 0)
                ->count();
            if ($parent) {
                return back()
                    ->withErrors('Mohon untuk menghapus child menu terlebih dahulu');
            }
            $menu->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }
}
