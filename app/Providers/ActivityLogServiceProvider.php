<?php

namespace App\Providers;

use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // IP Address on \Spatie\Activitylog\Models\Activity
        Activity::saving(function (Activity $activity) {
            $activity->properties = $activity->properties->put('ipaddress', request()->ip());
            $activity->properties = $activity->properties->put('url', request()->fullUrl());
            $activity->properties = $activity->properties->put('useragent', request()->header('User-Agent'));
        
            // Check impersonate
            $manager = app('impersonate');
            if($manager->isImpersonating()) {
                $activity->properties = $activity->properties->put('impersonator', $manager->getImpersonator());
            }
        });
    }
}
