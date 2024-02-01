<?php

namespace App\Models\Master\Location;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Province extends Model
{
    protected $table = 'm_province';
    protected $fillable = ['id', 'code', 'name', 'country_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly(['country_id','code','name']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Provinsi dengan nama :subject.name',
            'updated' => 'Ubah data Provinsi dengan nama :subject.name',
            'deleted' => 'Hapus data Provinsi dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function regencys()
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }
}
