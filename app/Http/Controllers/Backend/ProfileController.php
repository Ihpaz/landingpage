<?php

namespace App\Http\Controllers\Backend;

use Image;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Master\Location\Country;
use App\Models\User;
use App\Models\PasswordStash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Activitylog\Models\Activity;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Profile';
        // END MANDATORY PARAMETER

        // Address
        $data['country'] = Cache::remember('master:country:all', config('cache.lifetime'), function () {
            return Country::orderBy('name')->get();
        });

        $activities = Activity::where('causer_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('log_name', 'Login')
                    ->orWhere('log_name', 'Logout');
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($activities as $activity) {
            // Change icons
            $icons = 'fa fa-circle';
            switch ($activity->log_name) {
                case 'Login':
                    $icons = 'fa fa-sign-in';
                    break;
                case 'Logout':
                    $icons = 'fa fa-sign-out';
                    break;
                case 'Create':
                    $icons = 'fa fa fa-envelope-open-o';
                    break;
                case 'Update':
                    $icons = 'fa fa-pencil';
                    break;
                case 'Delete':
                    $icons = 'fa fa-trash-o';
                    break;
                default:
                    break;
            }
            $activity->icons = $icons;
        }
        $data['activities'] = $activities;

        return view('backend.profile.index', $data);
    }

    public function edit()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Profile';
        // END MANDATORY PARAMETER

        return view('backend.profile.edit', $data);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'thumbnail_photo' => 'image|max:1024',
            'old_password' => 'required_with:password',
            'password' => 'nullable|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/'
        ]);

        try {
            $user = User::findOrFail(auth()->user()->id);
            $user->fullname = $request->fullname;
            $user->nickname = $request->nickname;
            $user->nip = $request->nip;
            $user->phonenumber = $request->phonenumber;
            $user->company = $request->company;
            $user->department = $request->department;
            $user->position = $request->position;
            if ($request->has('thumbnail_photo')) {
                // Resize
                $image = $request->file('thumbnail_photo');
                $filename = $image->getClientOriginalName();
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $user->thumbnail_photo = base64_encode($image_resize->stream());
            }
            if ($request->input('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil diperbarui')->autoclose(3000);
            return redirect()
                ->route('backend.profile.index');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }
}
