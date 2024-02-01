<?php

namespace App\Models;

use App\Helpers\HumanReadable;
use App\Traits\LogsActivity;
use App\Traits\TimestampDiffHuman;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;

class Attachment extends Model
{
    use LogsActivity;

    protected $table = 'attachments';
    protected $keyType = 'string';
    public $incrementing = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Create attachment for :subject.source_type',
            'updated' => 'Edit attachment for :subject.source_type',
            'deleted' => 'Delete attachment for :subject.source_type'
        ];
        return $description[$eventName];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly(['source_type', 'source_id', 'filename', 'path', 'mime', 'module']);
    }

    public function scopeUploadFile(Builder $builder, Model $model, $module, UploadedFile $file, $public = false)
    {
        $id = Str::uuid();
        $path = strtolower($module) . '/' . date('Y/m/d');
        $fullpath = $path . '/' . $id . '.' . $file->getClientOriginalExtension();

        Storage::putFileAs($path, $file, $id . '.' . $file->getClientOriginalExtension());

        $this->id = $id;
        $this->filename = $file->getClientOriginalName();
        $this->path = $fullpath;
        $this->module = $module;
        $this->mime = $file->getMimeType();
        $this->is_public = $public;
        $this->source()->associate($model);
    }

    public function getIsExistsAttribute()
    {
        return Storage::exists($this->path);
    }

    public function scopeUnlinkFile()
    {
        if (Storage::exists($this->path)) {
            unlink(Storage::path($this->path));
        }
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function getSizeAttribute()
    {
        return HumanReadable::bytesToHuman(Storage::size($this->path));
    }
}
