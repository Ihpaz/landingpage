<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleProviderCallback(Request $request)
    {
        try {
            $user_provider = Socialite::driver('google')->user();
            $user_check = User::where('email', $user_provider->getEmail())->first();

            $user = new User;
            if ($user_check) {
                $user = $user_check;
            } else {
                $user->locale = 'id';
                $user->timezone = config('app.timezone');
                $user->status = 'ACTV';
            }
            $user->guid = $user_provider->getId();
            $user->avatar_url = $user_provider->getAvatar();
            $user->email = $user_provider->getEmail();
            $user->fullname = $user_provider->getName();
            $user->nickname = $user_provider->getNickname();
            $user->password = bcrypt(str_random(50));
            $user->disableLogging();
            $user->save();

            auth()->login($user);

            // Check if auth correct
            if (auth()->check()) {
                // User last login at
                auth()->user()->last_login_at = Carbon::now();
                auth()->user()->last_login_ip = $request->ip();
                auth()->user()->disableLogging();
                auth()->user()->save();

                ActivityLog::log('Login', ':causer.fullname has been login using OAuth2 Protocol');
                return redirect()->intended(route('backend.dashboard.index'));
            }
        } catch (Exception $e) {
            ActivityLog::sentry($e);
            return redirect()->route('auth.login.index')
                ->withErrors($e->getMessage());
        }
    }

    public function redirectToZoomProvider()
    {

    }

    public function handleZoomProviderCallback(Request $request)
    {
        
    }
}
