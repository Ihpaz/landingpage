<?php

namespace App\Http\Controllers\Media;

use App\Helpers\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class AttachmentController extends Controller
{
    public function download($id)
    {
        $attachment = Attachment::findOrFail($id);

        if ($attachment->is_exists) {
            return Storage::download($attachment->path, $attachment->filename);
        }
    }

    public function downloadPublic($id)
    {
        $attachment = Attachment::where('id', $id)
            ->where('is_public', true)
            ->first();

        if ($attachment->is_exists) {
            return Storage::download($attachment->path, $attachment->filename);
        }
    }

    public function stream($id)
    {
        $attachment = Attachment::findOrFail($id);

        if ($attachment->filename) {
            return response()->file(Storage::path($attachment->path, $attachment->filename));
        }
    }

    public function destroy($id)
    {
        try {
            $attachment = Attachment::findOrFail($id);
            $attachment->delete();

            // Send session flash message
            Alert::success(trans('common.success'), 'Attachment telah berhasil dihapus.')->autoclose(5000);
            return back();
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return back()
                ->withErrors(trans('exceptions.generic'));
        }
    }

}
