<?php

namespace App\Http\Controllers\Mfa;

use App\Helpers\ActivityLog;
use App\Helpers\ResponseFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mfa\VerificationRequest;
use App\Models\Passport\Client;
use App\Models\User;
use App\Models\User\Authenticator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FAQRCode\Google2FA;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class AuthenticatorController extends Controller
{
    public function challenge(Request $request)
    {
        if (session('_previous.url')) {
            return redirect(session('_previous.url'));
        }
        return back();
    }

    public function verification(VerificationRequest $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->with('authenticator')
                ->first();
            if ($user) {
                $mfa = $user->authenticator;
                if (empty($mfa)) {
                    return ResponseFormat::error("User not set an authenticator");
                }
                foreach ($mfa as $authenticator) {
                    $google2fa = new Google2FA();
                    $valid = $google2fa->verifyKey($authenticator->secret, $request->verification);
                    if ($valid) {
                        $authenticator->last_ip = $request->ip();
                        $authenticator->last_used = Carbon::now();
                        $authenticator->save();
                        return ResponseFormat::ok("OTP is valid");
                    }
                }
                return ResponseFormat::error("OTP is invalid");
            }
            return ResponseFormat::error('User not found');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return ResponseFormat::error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $mfa = Authenticator::findOrFail(Hashids::decode($id)[0]);
            if (!$mfa->is_authorized('cms user-authenticator delete')) {
                return back()
                    ->withErrors(trans('exceptions.unauthorized'));
            }
            $mfa->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back()
                ->withInput(['tab' => 'mfa']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'mfa'])
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatable(Request $request)
    {
        $query = Authenticator::selectRaw('
                id, user_id, secret, last_ip, last_used, device
            ')
            ->when($request->has('user_id') && $request->user()->can('cms user-authenticator view'), function ($query) {
                $query->where('user_id', request('user_id'));
            }, function ($query) {
                $query->where('user_id', auth()->user()->id);
            });

        $data = datatables()->of($query)
            ->addColumn('secret', function ($data) {
                $len = strlen($data->secret) - 4;
                return str_repeat('*', $len) . substr($data->secret, $len);
            })
            ->addColumn('geoip', function ($data) {
                if ($data->last_ip) {
                    return $data->last_ip . ' <a class="text-info cursor-pointer" onclick="showModalDetail(\'' . route('backend.geo.ip', $data->last_ip) . '\')"><i class="fa fa-globe"></i></a>';
                }
                return '-';
            })
            ->addColumn('actions', function ($data) use ($request) {
                $action = array();
                if ($data->is_authorized('cms user-authenticator delete')) {
                    array_push($action, '<button class="btn btn-outline-danger btn-xs" onclick="showModalDelete(\'' . $data->device . '\',\'' . route('mfa.otp.destroy', Hashids::encode($data->id)) . '\')" title="Delete"><i class="fa fa-trash-o"></i></button>');
                }
                return implode(' ', $action);
            })
            ->rawColumns(['geoip', 'actions']);

        return $data->make(true);
    }
}
