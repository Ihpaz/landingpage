<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class IpGeolocationController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ip)
    {
        $position = Location::get($ip);
        
        $data['ip'] = $ip;
        $data['countryName'] =  $position ? $position->countryName : '-';
        $data['countryCode'] =  $position ? $position->countryCode : '-';
        $data['regionCode'] =  $position ? $position->regionCode : '-';
        $data['regionName'] =  $position ? $position->regionName : '-';
        $data['cityName'] =  $position ? $position->cityName : '-';
        $data['zipCode'] =  $position ? $position->zipCode : '-';
        $data['isoCode'] =  $position ? $position->isoCode : '-';
        $data['latitude'] =  $position ? $position->latitude : '-';
        $data['longitude'] =  $position ? $position->longitude : '-';
        $data['timezone'] =  $position ? $position->timezone : '-';

        return view('backend.geoip.show', $data);
    }
}
