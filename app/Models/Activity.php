<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as BaseActivity;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Jenssegers\Agent\Agent;

class Activity extends BaseActivity
{
    public function subject(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    public function getIpAddressAttribute()
    {
        $decode = json_decode($this->attributes['properties']);
        return isset($decode->ipaddress) ? $decode->ipaddress : '-';
    }

    public function getPlatformAttribute()
    {
        $decode = json_decode($this->attributes['properties']);
        $agent = new Agent();
        return $agent->platform($decode->useragent);
    }

    public function getBrowserAttribute()
    {
        $decode = json_decode($this->attributes['properties']);
        $agent = new Agent();
        return $agent->browser($decode->useragent);
    }

    public function getDeviceAttribute()
    {
        $decode = json_decode($this->attributes['properties']);
        $agent = new Agent();
        return $agent->device($decode->useragent);
    }

    public function getIsPhoneAttribute()
    {
        $decode = json_decode($this->attributes['properties']);
        $agent = new Agent();
        return $agent->isPhone($decode->useragent);
    }
}
