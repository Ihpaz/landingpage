<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ActivityLog;
use App\Models\User;
use App\Models\Notification;
use App\Helpers\ResponseFormat;
use App\Helpers\HumanReadable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\DatabaseNotificationRequest;
use App\Jobs\SendUserNotification;
use App\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Notifications';
        // END MANDATORY PARAMETER

        return view('backend.notification.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Notifications';
        // END MANDATORY PARAMETER

        return view('backend.notification.show', $data);
    }

    public function userUnreadMessages()
    {
        $notifications = auth()->user()->unreadNotifications;
        foreach ($notifications as $notif) {
            $from = $notif['data']['from'] ?? 'System';
            $notif['from'] = '<strong>' . $from . '</strong>';
            $notif['message'] = $notif['data']['message'];
            $notif['img_thumbnail'] = ($from == 'System') ? asset('img/cms.png') : $notif['data']['img_thumbnail'];
            $notif['human_created_at'] = $notif->created_at->diffForHumans();

            // Remove unecessary attributes
            unset($notif['type']);
            unset($notif['notifiable_id']);
            unset($notif['notifiable_type']);
            unset($notif['updated_at']);
        }

        return $notifications->take(10)->reverse()->values();
    }

    public function redirectUrlNotification($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('backend.notification.index'));
    }

    public function alertCountUserNotification()
    {
        return auth()->user()->unreadNotifications->count();
    }

    private function alertCountUserApproval()
    {
        return Cache::remember('cms:count:approval:user', config('cache.notification'), function () {
            $data['url'] = route('cms.user.index');
            $data['color'] = 'info';
            $data['count'] = User::where('status', 'INAC')->count() > 1000 ? '+1K' : User::where('status', 'INAC')->count();
            return $data;
        });
    }

    public function notifications()
    {
        $counter = array();
        $notify = array();
        if (auth()->user()->can('cms user-management edit')) {
            array_push($counter, $this->alertCountUserApproval());
        }

        $data['counter'] = $counter;
        $data['notify'] = $notify;
        $data['total_notification'] = (int) $this->alertCountUserNotification();
        $data['notification'] = $this->userUnreadMessages();

        return ResponseFormat::ok($data);
    }

    public function notificationMessage(DatabaseNotificationRequest $request)
    {
        try {
            $list_user = explode(',', $request->to_user);
            $to_user = User::whereIn('email', $list_user)->get();

            $notification['from'] = $request->from_user;
            $notification['subject'] = $request->subject;
            $notification['message'] = $request->message;
            $notification['url'] = $request->url;

            foreach ($to_user as $to) {
                dispatch(new SendUserNotification($to, new DatabaseNotification($notification)));
            }
            return ResponseFormat::ok('Pesan telah dikirim');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $query = auth()->user()->notifications;

        $data = datatables()->of($query)
            ->addColumn('from', function ($data) {
                return $data->data['from'] ?? 'System';
            })
            ->addColumn('subject', function ($data) {
                return '<span class="label label-info" style="margin:1px">' . ucwords($data->data['subject'] ?? 'Information') . '</span>';
            })
            ->addColumn('time', function ($data) {
                return $data->created_at . '<p class="m-0"><small style="white-space: normal">' . $data->created_at->diffForHumans() . '</small></p>';
            })
            ->rawColumns(['from', 'subject', 'time'])
            ->make(true);
        return $data;
    }
}
