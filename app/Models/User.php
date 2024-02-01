<?php

namespace App\Models;

use App\Models\User\Address;
use App\Models\User\Authenticator;
use App\Models\User\Email;
use App\Traits\Encryptable;
use App\Traits\LogsActivity;
use App\Traits\TimestampDiffHuman;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity, Impersonate, HasPermissions, TimestampDiffHuman, Encryptable;

    protected $table = 'users';
    protected $fillable = [
        'email', 'password',
        'fullname', 'phonenumber',
        'company', 'department', 'position',
        'nip', 'pernr', 'nik', 'npwp', 'guid',
        'thumbnail_photo',
        'status'
    ];

    protected $hidden = ['password', 'remember_token', 'thumbnail_photo'];
    protected $encryptable = [];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy(
            'created_at',
            'desc'
        );
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly([
                'email',
                'fullname', 'phonenumber',
                'company', 'department', 'position',
                'nip', 'pernr', 'nik', 'npwp', 'guid',
                'thumbnail_photo',
                'status'
            ]);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data User dengan nama :subject.fullname',
            'updated' => 'Ubah data User dengan nama :subject.fullname',
            'deleted' => 'Hapus data User dengan nama :subject.fullname',
        ];
        return $description[$eventName];
    }

    public function getDefaultGuardName(): string
    {
        return 'web';
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'user_id', 'id');
    }

    public function address()
    {
        return $this->hasMany(Address::class, 'user_id', 'id');
    }

    public function authenticator()
    {
        return $this->hasMany(Authenticator::class, 'user_id', 'id');
    }

    public function getDefaultLocaleAttribute()
    {
        return $this->locale ?? config('app.locale');
    }

    public function getTimezoneAttribute()
    {
        return $this->timezone ?? config('app.timezone');
    }

    public function getUserThumbnailAttribute()
    {
        if ($this->thumbnail_photo) {
            return 'data:image/jpeg;base64,' . $this->thumbnail_photo;
        }
        if ($this->avatar_url) {
            return $this->avatar_url;
        }
        return asset('img/default-user.png');
    }


    public function getMinimumRolesLevelAttribute()
    {
        if (!empty($this->roles)) {
            return min($this->roles->pluck('level')->toArray());
        }
        return 9;
    }

    public function getMaximumRolesLevelAttribute()
    {
        if (!empty($this->roles)) {
            return max($this->roles->pluck('level')->toArray());
        }
    }

    public function getIsOnlineAttribute()
    {
        return Cache::has('userOnline-' . $this->id);
    }
}
