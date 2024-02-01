<?php

namespace App\Models\Master\Location;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Currency extends Model
{
    use LogsActivity;

    protected $table = 'm_currency';
    protected $fillable = ['id', 'code', 'symbol', 'name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly(['code','symbol','name']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Mata Uang dengan nama :subject.name',
            'updated' => 'Ubah data Mata Uang dengan nama :subject.name',
            'deleted' => 'Hapus data Mata Uang dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'm_country_currency');
    }
}
