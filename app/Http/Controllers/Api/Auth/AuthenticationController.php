<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateTokenRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Parser;

class AuthenticationController extends Controller
{
    use SendsPasswordResetEmails;

    public function createToken(CreateTokenRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Email atau password tidak sesuai'],
                ]);
            }

            $result['token'] = $user->createToken($request->device_name)->accessToken;
            return ResponseFormat::ok($result, "Token berhasil digenerate");
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }

    public function revokeToken(Request $request)
    {
        try {
            $accessToken = $request->bearerToken();
            $token_id = app(Parser::class)->parse($accessToken)->claims()->get('jti');
            Passport::token()->where('id', $token_id)->update(['revoked' => true]);

            return ResponseFormat::ok("Token berhasil di revoke");
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }

    public function revokeAllToken(Request $request)
    {
        try {
            Passport::token()->where('user_id', $request->user()->id)->update(['revoked' => true]);

            return ResponseFormat::ok("Token berhasil di revoke");
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }

    public function me(Request $request)
    {
        try {
            $query = User::with('roles:id,name,description')
                ->findOrFail($request->user()->id);
            
            return ResponseFormat::ok($query);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }
   
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->old_password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Email atau password tidak sesuai'],
                ]);
            }
            
            $user->password = bcrypt($request->password);
            $user->disableLogging();
            $user->save();
            
            return ResponseFormat::ok('Password berhasil disimpan');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {       
        $response = Password::broker()->sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}
