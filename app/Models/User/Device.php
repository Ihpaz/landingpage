<?php

namespace App\Models\User;

use App\Jobs\SendUserNotification;
use App\Models\User;
use App\Notifications\User\SuspiciousAccessNotification;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;

class Device extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'user_devices';

    protected $casts = [
        'last_used' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly([
                'user_id', 'ip', 'device', 'browser', 'platform', 'imei', 'is_block', 'useragent'
            ]);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Device dengan nama :subject.device',
            'updated' => 'Ubah data Device dengan nama :subject.device',
            'deleted' => 'Hapus data Device dengan nama :subject.device',
        ];
        return $description[$eventName];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getIsPhoneAttribute()
    {
        $agent = new Agent();
        return $agent->isPhone($this->useragent);
    }

    public function getIsRobotAttribute()
    {
        $agent = new Agent();
        return $agent->isRobot($this->useragent);
    }

    public static function isSuspiciousActivity(User $user, Request $request)
    {
        $device = self::where('user_id', $user->id)
            ->where('ip', $request->ip())
            ->first();
        if (!$device) {
            $agent = new Agent();
            $platform = $agent->platform($request->userAgent());
            $browser = $agent->browser($request->userAgent());
            $platform_browser = $platform . ' ' . $browser;
            $carbon = Carbon::now()->locale('id');
            $carbon->settings(['formatFunction' => 'translatedFormat']);
            $carbon->format('l, j F Y  H:i:s');
            $datetime = $carbon . " WIB";

            dispatch(new SendUserNotification($user, new SuspiciousAccessNotification($datetime, $request->ip(), $user->email, $platform_browser)));

            $device = new self();
            $device->user_id = $user->id;
            $device->ip = $request->ip();
            $device->device = $agent->device($request->userAgent());
            $device->browser = $browser;
            $device->useragent = $request->userAgent();
            $device->platform = $platform;
            $device->last_used = Carbon::now();
            $device->disableLogging();
            $device->save();
        } else {
            $agent = new Agent();
            $platform = $agent->platform($request->userAgent());
            $browser = $agent->browser($request->userAgent());
            $device->ip = $request->ip();
            $device->device = $agent->device($request->userAgent());
            $device->browser = $browser;
            $device->useragent = $request->userAgent();
            $device->platform = $platform;
            $device->last_used = Carbon::now();
            $device->disableLogging();
            $device->save();

            if ($device->is_block) {
                return true;
            }
        }
        return false;
    }

    public function getIsOutsideIndonesiaRegionAttribute()
    {
        // Indonesia lat-long
        if ($this->longitude <= 95 || $this->longitude >= 141) {
            return true;
        }
        if ($this->latitude <= -11 || $this->latitude >= 6) {
            return true;
        }
        return false;
    }
}
