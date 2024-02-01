<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use App\Jobs\SendUserEmail;
use App\Mail\User\EmailVerification;
use App\Models\User;
use App\Models\User\Address;
use App\Models\User\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class PersonalController extends Controller
{
    public function verifyEmail($secret)
    {
        try {
            $email = Email::where('secret', $secret)
                ->where('is_active', false)
                ->first();
            if ($email) {
                $email->is_active = true;
                $email->save();

                Alert::success(trans('common.success'), 'Email berhasil diverfiikasi');
                return back();
            }
            Alert::warning("Warning", "Email token salah atau sudah pernah digunakan sebelumnya");
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEmail(Request $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->first();
            if ($user) {
                return back()
                    ->withInput(['tab' => 'personal'])
                    ->withErrors("Email has been used please register another email");
            }
            $email = new Email();
            $email->user_id = auth()->user()->id;
            $email->email = $request->email;
            $email->type = $request->type;
            $email->is_active = false;
            $email->secret = Str::random(32);
            $email->save();

            dispatch(new SendUserEmail($email->email, new EmailVerification($email->secret)));

            Alert::success("Success", "Data berhasil ditambahkan");
            return back()
                ->withInput(['tab' => 'personal']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'personal'])
                ->withErrors($e->getMessage());
        }
    }

    public function sendEmailVerification($id)
    {
        try {
            $email = Email::findOrFail(Hashids::decode($id)[0]);

            dispatch(new SendUserEmail($email->email, new EmailVerification($email->secret)));

            Alert::success("Success", "Email berhasil dikirim");
            return back()
                ->withInput(['tab' => 'personal']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'personal'])
                ->withErrors($e->getMessage());
        }
    }

    public function storeAddress(Request $request)
    {
        try {
            $address = new Address();
            $address->user_id = auth()->user()->id;
            $address->country_id = $request->country_id;
            $address->province_id = $request->province_id;
            $address->regency_id = $request->regency_id;
            $address->address = $request->address;
            $address->save();

            Alert::success("Success", "Data berhasil ditambahkan");
            return back()
                ->withInput(['tab' => 'personal']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'personal'])
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editAddress($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAddress(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyEmail($id)
    {
        try {
            $email = Email::findOrFail(Hashids::decode($id)[0]);
            $email->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back()
                ->withInput(['tab' => 'personal']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'personal'])
                ->withErrors($e->getMessage());
        }
    }

    public function destoryAddress($id)
    {
        try {
            $address = Address::findOrFail(Hashids::decode($id)[0]);
            $address->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil dihapus.')->autoclose(5000);
            return back()
                ->withInput(['tab' => 'personal']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'personal'])
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Yajra\Datatables\Facades\Datatables
     */
    public function datatableEmail(Request $request)
    {
        $query = Email::selectRaw('
                id, email, is_active, type
            ')
            ->when(request('user_id') && $request->user()->can('cms user-email view'), function ($query) {
                $query->where('user_id', request('user_id'));
            }, function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });

        $data = datatables()->of($query)
            ->addColumn('status', function ($data) {
                if ($data->is_active) {
                    return '<span class="label label-info bg-green mb-0">Verified</span>';
                }
                return ' <span class="label label-inverse mb-0">Waiting</span>';
            })
            ->addColumn('actions', function ($data) use ($request) {
                $action = array();
                array_push($action, '<a href="' . route('backend.personal.email.send', Hashids::encode($data->id)) . '" class="btn btn-secondary btn-xs" title="Send"><i class="fa fa-envelope-o"></i></a>');
                array_push($action, '<button class="btn btn-outline-danger btn-xs" onclick="showModalDelete(\'' . $data->email . '\',\'' . route('backend.personal.email.destroy', \Hashids::encode($data->id)) . '\')" title="Delete"><i class="fa fa-trash-o"></i></button>');
                
                return implode(' ', $action);
            })
            ->rawColumns(['status', 'actions']);

        return $data->make(true);
    }

    public function datatableAddress(Request $request)
    {
        $query = Address::selectRaw('
                id, user_id, country_id, province_id, regency_id, district_id, village_id, address, created_at
            ')
            ->with('country', 'province', 'regency')
            ->when($request->has('user_id') && $request->user()->can('cms user-address view'), function ($query) {
                $query->where('user_id', request('user_id'));
            }, function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });

        $data = datatables()->of($query)
            ->addColumn('actions', function ($data) use ($request) {
                $action = array();
                array_push($action, '<button class="btn btn-outline-danger btn-xs" onclick="showModalDelete(\'' . 'Address' . '\',\'' . route('backend.personal.address.destroy', \Hashids::encode($data->id)) . '\')" title="Delete"><i class="fa fa-trash-o"></i></button>');

                return implode(' ', $action);
            })
            ->rawColumns(['status', 'actions']);

        return $data->make(true);
    }
}
