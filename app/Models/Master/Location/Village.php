<?php

namespace App\Models\Master\Location;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Village extends Model
{
    use LogsActivity;

    protected $table = 'm_village';
    protected $fillable = ['id', 'code', 'district_id', 'name', 'pos_code'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly(['district_id', 'code', 'name', 'pos_code']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Kelurahan dengan nama :subject.name',
            'updated' => 'Ubah data Kelurahan dengan nama :subject.name',
            'deleted' => 'Hapus data Kelurahan dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
}
