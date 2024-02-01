<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class DocumentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $file = $request->file('file');
            $attachment = new Attachment;
            $attachment->uploadFile(auth()->user(), $request->type, $file);
            $attachment->save();

            Alert::success("Success", "Data berhasil ditambahkan");
            return back()
                ->withInput(['tab' => 'document']);
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withInput(['tab' => 'document'])
                ->withErrors($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function datatable(Request $request)
    {
        $query = Attachment::select()
            ->where('source_type','App\Models\User')
            ->when(request('user_id'), function ($query) {
                $query->where('source_id', request('user_id'));
            }, function ($query) use ($request) {
                $query->where('source_id', $request->user()->id);
            });

        $data = datatables()->of($query)
            ->addColumn('size', function ($data) {
                return $data->size;
            })
            ->addColumn('diperbarui', function ($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('actions', function ($data) use ($request) {
                $action = array();
                if($data->mime == 'application/pdf') {
                    array_push($action, '<button class="btn btn-outline-info btn-xs" onclick="showModalEmbedXlHeight(\'' . route('attachment.stream', $data->id) . '\',\''.$data->mime.'\',800)" title="Delete"><i class="fa fa-eye"></i></button>');

                } else {
                    array_push($action, '<button class="btn btn-outline-info btn-xs" onclick="showModalEmbedXl(\'' . route('attachment.stream', $data->id) . '\',\''.$data->mime.'\')" title="Delete"><i class="fa fa-eye"></i></button>');
                }
                array_push($action, '<button class="btn btn-outline-danger btn-xs" onclick="showModalDelete(\'' . $data->filename . '\',\'' . route('attachment.destroy', $data->id) . '\')" title="Delete"><i class="fa fa-trash-o"></i></button>');
                
                return implode(' ', $action);
            })
            ->rawColumns(['actions']);

        return $data->make(true);
    }
}
