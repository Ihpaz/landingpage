<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use RealRashid\SweetAlert\Facades\Alert;

class AccessTokenController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $token = Token::findOrFail($id);
            $token->revoked = true;
            $token->save();

            // Send session flash message
            Alert::success(trans('common.success'), 'Data telah berhasil direvoke.')->autoclose(5000);

            return back()
                ->withInput(['tab' => 'access_token']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'access_token'])
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
        $query = Token::selectRaw('
                oauth_access_tokens.id, oauth_access_tokens.user_id, client_id, oauth_access_tokens.revoked, 
                oauth_access_tokens.name, oauth_clients.name as client,
                oauth_access_tokens.created_at, oauth_access_tokens.expires_at
            ')
            ->join('oauth_clients', 'oauth_clients.id', '=', 'oauth_access_tokens.client_id')
            ->where('oauth_access_tokens.revoked', false)
            ->where('oauth_access_tokens.expires_at', '>', Carbon::now())
            ->when($request->has('user_id') && $request->user()->can('cms user-token view'), function ($query) {
                $query->where('oauth_access_tokens.user_id', request('user_id'));
            }, function ($query) use ($request) {
                $query->where('oauth_access_tokens.user_id', $request->user()->id);
            });

        $data = datatables()->of($query)
            ->addColumn('id', function ($data) {
                $len = strlen($data->id) - 6;
                return str_repeat('*', $len - 6) . substr($data->id, $len);
            })
            ->addColumn('created_at', function ($data) {
                return Carbon::parse($data->expires_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('expires_at', function ($data) {
                return Carbon::parse($data->expires_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('status', function ($data) {
                if ($data->revoked) {
                    return '<i class="fa fa-times text-danger"></i>';
                }
                if (!Carbon::now()->lte($data->expired_at)) {
                    return '<i class="fa fa-times text-danger"></i>';
                }
                return '<i class="fa fa-check-circle text-success"></i>';
            })
            ->addColumn('actions', function ($data) use ($request) {
                $action = array();
                if ($request->user()->can('cms user-management delete')) {
                    if (!$data->revoked) {
                        array_push($action, '<button class="btn btn-outline-danger btn-xs" onclick="showModalDelete(\'' . $data->created_at . '\',\'' . route('backend.access.token.destroy', $data->id) . '\')" title="Revoke"><i class="fa fa-times"></i></button>');
                    }
                }
                return implode(' ', $action);
            })
            ->rawColumns(['status', 'actions']);

        return $data->make(true);
    }
}
