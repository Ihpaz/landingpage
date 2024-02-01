<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecentLoginController extends Controller
{
    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $query = Activity::selectRaw('
                properties, created_at
            ')
            ->when($request->has('user_id') && $request->user()->can('cms user-device view'), function ($query) {
                $query->where('causer_id', request('user_id'));
            }, function ($query) use ($request) {
                $query->where('causer_id', $request->user()->id);
            })
            ->where('log_name', 'Login')
            ->when(request('date_start') && request('date_end'), function ($query) {
                $query->whereBetween(
                    'created_at',
                    [
                        Carbon::createFromFormat('d.m.Y', request('date_start')),
                        Carbon::createFromFormat('d.m.Y', request('date_end'))
                    ]
                );
            }, function ($query) {
                $query->whereDate('created_at', '>', Carbon::now()->subDay(7));
            });

        $data = datatables()->of($query)
            ->addColumn('display', function ($data) {
                if ($data->is_phone) {
                    $icon = ' <i class="fa fa-mobile"></i>';
                } else {
                    $icon = ' <i class="fa fa-desktop"></i>';
                }
                return $data->platform . ' ' . $data->browser . ' ' . $icon;
            })
            ->addColumn('type', function ($data) {
                if ($data->is_phone) {
                    return 'Phone';
                }
                return 'Browser';
            })
            ->addColumn('time', function ($data) {
                return $data->created_at;
            })
            ->addColumn('ip', function ($data) {
                return '<a class="text-info cursor-pointer" onclick="showModalDetail(\'' . route('backend.geo.ip', $data->ip_address) . '\')"><i class="fa fa-globe"></i></a> ' . $data->ip_address;
            })
            ->rawColumns(['display', 'type', 'ip'])
            ->make(true);
        return $data;
    }
}
