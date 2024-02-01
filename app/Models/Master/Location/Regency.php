<?php

namespace App\Models\Master\Location;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Regency extends Model
{
    use LogsActivity;

    protected $table = 'm_regency';
    protected $fillable = ['id','code', 'province_id', 'name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly(['province_id','code','name']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Kota dengan nama :subject.name',
            'updated' => 'Ubah data Kota dengan nama :subject.name',
            'deleted' => 'Hapus data Kota dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'regency_id', 'id');
    }
}
