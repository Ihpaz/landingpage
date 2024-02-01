<?php

namespace App\Http\Controllers\Cms;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Models\Menu\ModuleField;
use App\Models\Menu\ModuleFieldType;
use App\Models\Menu\Modules;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Vinkla\Hashids\Facades\Hashids;

class ModuleManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Module Management';
        // END MANDATORY PARAMETER

        return view('cms.module.index', $data);
    }

    public function indexField($id)
    {
        try {
            $module = Modules::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = 'Module';
            // END MANDATORY PARAMETER
            $data['module'] = $module;
            $data['type'] = ModuleFieldType::orderBy('name')->get();
            $data['field'] = ModuleField::where('module_id', $module->id)->orderBy('sort')->get();
            $schema_column = Schema::getColumnListing($module->table);
            $colname = ModuleField::where('module_id', $module->id)->pluck('colname')->all();
            $except_column = array_merge($colname, ['id', 'created_at', 'updated_at', 'deleted_at']);
            $column = collect($schema_column)->filter(function ($data) use ($except_column) {
                if (!in_array($data, $except_column)) {
                    return $data;
                }
            });
            $data['column'] = $column->flatten();
            $data['tables'] =  DB::table('pg_catalog.pg_tables')
                ->where('schemaname', 'public')
                ->orderBy('tablename')->get();

            return view('cms.module.field.index', $data);
        } catch (Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Create Module';
        // END MANDATORY PARAMETER
        $data['tables'] =  DB::table('pg_catalog.pg_tables')
            ->where('schemaname', 'public')
            ->orderBy('tablename')->get();

        return view('cms.module.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $module = new Modules;
            $module->name = $request->name;
            $module->slug = str_slug($request->name);
            $module->table = $request->table;
            $module->model = $request->model;
            $module->save();

            // Create default permission
            $permissions = [
                ['name' => 'module ' . $module->slug . ' view', 'description' => 'Menampilkan list data ' . $module->name, 'guard_name' => 'web'],
                ['name' => 'module ' . $module->slug . ' create', 'description' => 'Manambahkan data ' . $module->name, 'guard_name' => 'web'],
                ['name' => 'module ' . $module->slug . ' update', 'description' => 'Mengubah data ' . $module->name, 'guard_name' => 'web'],
                ['name' => 'module ' . $module->slug . ' delete', 'description' => 'Menghapus data ' . $module->name, 'guard_name' => 'web'],
            ];

            foreach ($permissions as $data) {
                Permission::updateOrCreate(['name' => $data['name']], $data);
            }

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.module.index');
        } catch (Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function storeField(Request $request, $id)
    {
        try {
            $field = new ModuleField;
            $field->colname = strtolower($request->colname);
            $field->label = $request->label;
            $field->module_id = Hashids::decode($id)[0];
            $field->field_type_id = $request->field_type_id;
            $field->unique = $request->has('unique') ? true : false;
            $field->required = $request->has('required') ? true : false;
            $field->listing_col = $request->has('listing_col') ? true : false;
            $field->comment = $request->comment;
            $field->minlength = $request->minlength;
            $field->maxlength = $request->maxlength;
            $field->default = $request->default;
            $field->sort = ModuleField::where('module_id', $field->module_id)->count() + 1;

            $ft_val = $field->field_type_id;
            if (in_array($ft_val, [1, 2, 6, 8, 9, 11, 13, 15, 16])) {
                $field->unique = false;
            }

            if (in_array($ft_val, [2, 3, 4, 6, 7, 8, 9, 11, 13, 16, 18, 19, 20])) {
                $field->minlength = false;
                $field->maxlength = false;
            }

            if (in_array($ft_val, [6, 11, 13, 19, 20])) {
                if ($request->from == 'list') {
                    $field->popup_vals = json_encode(explode(',', $request->values_list));
                }
                if ($request->from == 'table') {
                    $field->popup_vals = '@' . $request->values_table;
                }
            }
            $field->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return back();
        } catch (Exception $e) {
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
            $module = Modules::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = $module->name;
            // END MANDATORY PARAMETER
            $data['module'] = $module;
            $data['tables'] =  DB::table('pg_catalog.pg_tables')
                ->where('schemaname', 'public')
                ->orderBy('tablename')->get();

            return view('cms.module.edit', $data);
        } catch (Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function editField(string $id)
    {
        try {
            $field = ModuleField::findOrFail(Hashids::decode($id)[0]);
            // MANDATORY PARAMETER
            $data['title'] = $field->label;
            // END MANDATORY PARAMETER
            $data['field'] = $field;
            $data['tables'] =  DB::table('pg_catalog.pg_tables')
                ->where('schemaname', 'public')
                ->orderBy('tablename')->get();
            $schema_column = Schema::getColumnListing($field->module->table);

            $column = collect($schema_column)->filter(function ($data) {
                if (!in_array($data, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    return $data;
                }
            });
            $data['column'] = $column->flatten();

            return view('cms.module.field.edit', $data);
        } catch (Exception $e) {
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
            $module = Modules::findOrFail(Hashids::decode($id)[0]);            
            $module->name = $request->name;
            $module->slug = str_slug($request->name);
            $module->table = $request->table;
            $module->model = $request->model;
            $module->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return redirect()
                ->route('cms.module.index');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function updateField(Request $request, string $id)
    {
        try {
            $field = ModuleField::findOrFail(Hashids::decode($id)[0]);
            $field->colname = strtolower($request->colname);
            $field->label = $request->label;
            $field->unique = $request->has('unique') ? true : false;
            $field->required = $request->has('required') ? true : false;
            $field->listing_col = $request->has('listing_col') ? true : false;
            $field->comment = $request->comment;
            $field->minlength = $request->minlength;
            $field->maxlength = $request->maxlength;
            $field->default = $request->default;

            $ft_val = $field->field_type_id;
            if (in_array($ft_val, [1, 2, 6, 8, 9, 11, 13, 15, 16])) {
                $field->unique = false;
            }

            if (in_array($ft_val, [2, 3, 4, 6, 7, 8, 9, 11, 13, 16, 18, 19, 20])) {
                $field->minlength = false;
                $field->maxlength = false;
            }
            $field->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil disimpan')->autoclose(5000);
            return back();
        } catch (Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $json_menu = json_decode($request->menus, true);
            $order = 1;
            // Parent
            foreach ($json_menu as $menu) {
                $pid = $menu['id'];
                $pmenu = ModuleField::findOrFail($pid);
                $pmenu->sort = $order;
                $pmenu->disableLogging();
                $pmenu->save();

                $order++;
            }
            return ResponseFormat::ok($json_menu);
        } catch (Exception $e) {
            return ResponseFormat::error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $module = Modules::findOrFail(Hashids::decode($id)[0]);
            Permission::where('name', 'ILIKE', 'module ' . $module->slug . '%')->delete();

            $module->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    public function destroyField(string $id)
    {
        try {
            $module = ModuleField::findOrFail(Hashids::decode($id)[0]);
            $module->delete();

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
        $query = Modules::query();
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('diperbarui', function ($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('full_path_model', function ($data) {
                return $data->full_path_model;
            })
            ->addColumn('count', function ($data) {
                return DB::table($data->table)->count();
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                array_push($action, '<a href="' . route('cms.module.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                array_push($action, '<a href="' . route('cms.module.field.index', Hashids::encode($data->id)) . '" class="btn btn-outline-primary btn-xs" title="Ubah Field"><i class="fa fa-cogs"></i></a>');
                array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->name . '\',\'' . route('cms.module.destroy', Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');

                return implode(' ', $action);
            })
            ->rawColumns(['actions'])
            ->make(true);
        return $data;
    }

    public function datatableModuleField(Request $request)
    {
        $query = ModuleField::select()
            ->with('field')
            ->where('module_id', request('module_id'));
        $data = datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('unique', function ($data) {
                if ($data->unique) {
                    return '<label class="text-info">True</label>';
                }
            })
            ->addColumn('required', function ($data) {
                if ($data->required) {
                    return '<label class="text-info">True</label>';
                }
            })
            ->addColumn('listing', function ($data) {
                if ($data->listing_col) {
                    return '<label class="text-info">True</label>';
                }
            })
            ->addColumn('values', function ($data) {
                if (in_array($data->field_type_id, [6, 11, 13, 19, 20])) {
                    $values = json_decode($data->popup_vals);
                }
            })
            ->addColumn('actions', function ($data) {
                $action = array();
                array_push($action, '<a href="' . route('cms.module.field.edit', Hashids::encode($data->id)) . '" class="btn btn-outline-warning btn-xs" title="' . trans('common.edit') . '"><i class="fa fa-pencil"></i></a>');
                array_push($action, '<button class="btn btn-outline-danger btn-xs" data-toggle="modal" onclick="showModalDelete(\'' . $data->label . '\',\'' . route('cms.module.field.destroy', Hashids::encode($data->id)) . '\')" title="' . trans('common.delete') . '"><i class="fa fa-trash-o"></i></button>');

                return implode(' ', $action);
            })
            ->rawColumns(['unique', 'listing', 'required', 'actions'])
            ->make(true);
        return $data;
    }
}
