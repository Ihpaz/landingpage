<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MacsiDigital\Zoom\Facades\Zoom;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'Dashboard';
        // END MANDATORY PARAMETER

        return view('backend.dashboard.index', $data);
    }
}
