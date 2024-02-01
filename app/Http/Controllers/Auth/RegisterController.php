<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        $data['title'] = 'Register';

        return view('auth.register.user', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'fullname' => 'required',
            'company' => 'required',
            'title' => 'required',
            'phonenumber' => 'required',
            'password' => 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'captcha' => 'required|captcha'
        ]);

        // $user = new User;
        // $user->fullname = strtoupper($request->fullname);
        // $user->email = strtolower($request->email);
        // $user->company = $request->company;
        // $user->title = $request->title;
        // $user->phonenumber = $request->phonenumber;
        // $user->password = bcrypt($request->password);
        // $user->remember_token = str_random(100);
        // $user->is_sso = false;
        // $user->is_active_directory = false;
        // $user->is_sync = false;
        // $user->status = 'INAC';
        // $user->save();

        return $request;
    }
}
