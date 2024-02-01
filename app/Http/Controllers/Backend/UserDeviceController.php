<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\User\Device;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class UserDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {
        try {
            $device = Device::findOrFail(Hashids::decode($id)[0]);
            if ($device->is_authorized('cms user-device update')) {
                $device->is_block = $device->is_block ? false : true;
                $device->save();
            }

            return back()
                ->withInput(['tab' => 'device']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'device'])
                ->withErrors($e->getMessage());
        }
    }

    public function datatable(Request $request)
    {
        $query = Device::selectRaw('
                id, ip, platform, browser, last_used, is_block
            ')
            ->when($request->has('user_id') && $request->user()->can('cms user-device view'), function ($query) {
                $query->where('user_id', request('user_id'));
            }, function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });

        $data = datatables()->of($query)
            ->addColumn('name', function ($data) {
                if ($data->is_phone) {
                    $icon = ' <i class="fa fa-mobile"></i>';
                } else {
                    $icon = ' <i class="fa fa-desktop"></i>';
                }
                return $data->platform . ' ' . $data->browser . $icon;
            })
            ->addColumn('type', function ($data) {
                if ($data->is_phone) {
                    return 'Phone';
                }
                return 'Browser';
            })
            ->addColumn('time', function ($data) {
                return $data->last_used;
            })
            ->addColumn('geoip', function ($data) {
                return $data->ip . ' <a class="text-info cursor-pointer" onclick="showModalDetail(\'' . route('backend.geo.ip', $data->ip) . '\')"><i class="fa fa-globe"></i></a>';
            })
            ->addColumn('status', function ($data) {
                if ($data->is_block) {
                    return '<i class="fa fa-times text-danger"></i> Block';
                }
                return '<i class="fa fa-check-circle text-success"></i> Allow';
            })
            ->addColumn('actions', function ($data) use ($request) {
                $action = array();
                $icon = $data->is_block ? 'fa-unlock' : 'fa-lock';
                $title = $data->is_block ? 'Unlock' : 'Lock';
                array_push($action, '<a href="' . route('backend.device.status.toggle', \Hashids::encode($data->id)) . '" class="btn btn-secondary btn-xs" title="' . $title . '"><i class="fa ' . $icon . '"></i></a>');

                return implode(' ', $action);
            })
            ->rawColumns(['name', 'geoip', 'status', 'actions'])
            ->make(true);
        return $data;
    }
}
