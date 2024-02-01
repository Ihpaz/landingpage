<?php

namespace App\Models\User;

use App\Models\Master\Location\Country;
use App\Models\Master\Location\District;
use App\Models\Master\Location\Province;
use App\Models\Master\Location\Regency;
use App\Models\Master\Location\Village;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'user_address';

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }
}
