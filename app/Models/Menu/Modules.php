<?php

namespace App\Models\Menu;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Spatie\Activitylog\LogOptions;

class Modules extends Model
{
    use LogsActivity;

    protected $table = 'cms_modules';
    protected $model_namespace = 'App\Models';
    protected $controller_namespace = 'App\Http\Controllers';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'table', 'model', 'controller']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Module dengan nama :subject.name',
            'updated' => 'Ubah data Module dengan nama :subject.name',
            'deleted' => 'Hapus data Module dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function fields()
    {
        return $this->hasMany(ModuleField::class, 'module_id', 'id');
    }

    public function getBaseModelAttribute()
    {
        return class_basename($this->model);
    }

    public function getFullPathModelAttribute()
    {
        return 'App/Models/' . $this->model;
    }
}
