<?php

namespace App\Http\Controllers\Auth;

use Adldap\Auth\BindException;
use Adldap\Laravel\Facades\Adldap;
use App\Models\User;
use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateTokenRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function index()
    {
        $data['title'] = 'Login';

        return view('auth.login', $data);
    }

    /**
     * Handle am admin login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'  => 'required',
            'password'  => 'required',
            'captcha'   => 'required|captcha'
        ]);

        $username_ad = $request->email;
        $password_ad = $request->password;
        try {
            if (config('ldap.enable')) {
                if (!Adldap::auth()->attempt($username_ad, $password_ad)) {
                    return back()->withInput()->withErrors('The email or password is incorrect.');
                }
                // Finding a record.
                $user_ad = Adldap::search()->users()->findBy('mail', $username_ad);

                $user_check = User::where('email', strtolower($username_ad))->first();
                $user = new User;
                if ($user_check) {
                    // Existing user
                    $user = $user_check;
                } else {
                    // Status false
                    $user->status = 'ACTV';
                }

                $nip = preg_match('/^[0-9]{7,9}([A-Z]{0,3})/', $user_ad->getFirstAttribute('description'));
                $user->fullname = strtoupper($user_ad->getFirstAttribute('displayname'));
                $user->nip = $user->nip ?? ($nip ? $user_ad->getFirstAttribute('description') : null);
                $user->thumbnail_photo = $user->thumbnail_photo ?? ($user_ad->getFirstAttribute('thumbnailphoto') ? base64_encode($user_ad->getFirstAttribute('thumbnailphoto')) : null);
                $user->phonenumber = $user_ad->getFirstAttribute('phonenumber') ?? null;
                $user->email = strtolower($user_ad->getFirstAttribute('mail'));
                $user->position = $user_ad->getFirstAttribute('title') ?? null;
                $user->company = $user_ad->getFirstAttribute('company') ?? null;
                $user->department = $user_ad->getFirstAttribute('department') ?? null;
                $user->ad_objectguid = $user_ad->getFirstAttribute('objectgui') ?? null;
                $user->password = bcrypt($password_ad);
                $user->remember_token = str_random(100);
                $user->is_active_directory = true;

                $user->disableLogging();
                $user->save();

                // Check if user in active
                if ($user->status != 'ACTV') {
                    $message = 'Maaf, user anda sedang tidak aktif. Mohon menunggu atau menghubungi admin.';
                    ActivityLog::logWithProperty('Failed Login', 'Akun ' . $user->email . ' terdeteksi login LDAP dengan status INAC.', $user);
                    return redirect()->route('auth.login.index')->withErrors($message);
                }

                // Check if user in active date
                if (!$user->in_active_date) {
                    $message = 'Maaf, user anda dalam masa non aktif. Silahkan hubungi admin.';
                    return redirect()->route('auth.login.index')->withErrors($message);
                }

                // Assign role
                if (!count($user->getRoleNames())) {
                    // Add guest role
                    $user->assignRole('guest');
                }
            }

            // Credentials login type
            $loginType = filter_var($username_ad, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $credential = [
                $loginType => $username_ad,
                'password' => $password_ad
            ];

            // Attempt to log the user in
            if (auth()->attempt($credential)) {
                // Check if auth correct
                if (auth()->check()) {
                    // User last login at
                    auth()->user()->last_login_at = Carbon::now();
                    auth()->user()->last_login_ip = $request->ip();
                    auth()->user()->disableLogging();
                    auth()->user()->save();

                    ActivityLog::log('Login', ':causer.fullname has been login');
                    return redirect()->intended(route('backend.dashboard.index'));
                };
            }
            // if unsuccessful, then redirect back to the login with the form data
            return back()->withInput()->withErrors(trans('auth.failed'));
        } catch (BindException $e) {
            ActivityLog::sentry($e);
            return back()->withInput()->withErrors($e->getMessage());
        } catch (QueryException $e) {
            ActivityLog::sentry($e);
            return redirect()->route('auth.login.index')
                ->withErrors(trans('exceptions.query'));
        } catch (Exception $e) {
            ActivityLog::sentry($e);
            return redirect()->route('auth.login.index')
                ->withErrors(trans($e->getMessage()));
        }
    }
    
    /**
     * Log the admin out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (auth()->check()) {
            ActivityLog::logout();
            auth()->user()->disableLogging();
            auth()->logout();
        }
        $request->session()->invalidate();
        return redirect()->route('auth.login.index');
    }
}
