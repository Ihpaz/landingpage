<?php

namespace App\Models\Master\Location;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class District extends Model
{
    use LogsActivity;

    protected $table = 'm_district';
    protected $fillable = ['id', 'code', 'name', 'regency_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly(['regency_id', 'code', 'name']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Kecamatan dengan nama :subject.name',
            'updated' => 'Ubah data Kecamatan dengan nama :subject.name',
            'deleted' => 'Hapus data Kecamatan dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'district_id', 'id');
    }
}
