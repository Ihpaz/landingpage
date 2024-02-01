<?php

namespace App\Models\Master\Location;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Country extends Model
{
    use LogsActivity;

    protected $table = 'm_country';
    protected $fillable = ['id', 'code', 'name', 'alpha_2', 'alpha_3'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly(['code','name','alpha_2','alpha_3','capital']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Negara dengan nama :subject.name',
            'updated' => 'Ubah data Negara dengan nama :subject.name',
            'deleted' => 'Hapus data Negara dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function provincies()
    {
        return $this->hasMany(Province::class, 'country_id', 'id');
    }

    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'm_country_currency');
    }
}
