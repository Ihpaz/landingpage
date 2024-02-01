<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
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
            
            $result['user'] = $user;
            $result['token'] = $user->createToken('Google OAuth')->accessToken;

            return ResponseFormat::ok($result);
        } catch (Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }
}
