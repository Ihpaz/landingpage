<?php

namespace App\Models\Auth;

use Adldap\Adldap;
use App\Helpers\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordStash extends Model
{
    use HasFactory;

    protected $table = 'cms_password_stash';
    protected $dates = ['expired_at'];

    public const PASSWORD_AGE = 30;
    public const PASSWORD_HISTORY = 15;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function insertOrRejectStash(User $user, $password)
    {
        if (!PasswordStash::isDuplicatePassword($user, $password)) {
            try {
                PasswordStash::insertStash($user, $password);
            } catch (\Exception $e) {
                ActivityLog::sentry($e);
                return false;
            }
            return true;
        }
        return false;
    }

    public static function insertStash(User $user, $password)
    {
        try {
            DB::beginTransaction();
            $stash = new PasswordStash;
            $stash->user_id = $user->id;
            $stash->password = bcrypt($password);
            $stash->expired_at = Carbon::now()->addDays(PasswordStash::PASSWORD_AGE);
            $stash->save();

            DB::commit();
        } catch (\Exception $e) {           
            DB::rollBack();
            ActivityLog::sentry($e);
        }
    }

    public static function isDuplicatePassword(User $user, $password)
    {
        $stash = PasswordStash::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(PasswordStash::PASSWORD_HISTORY)
            ->get();
        if (empty($stash)) {
            return false;
        }
        foreach ($stash as $data) {
            if (Hash::check($password, $data->password)) {
                return true;
            }
        }
        return false;
    }

    public function getIsExpiredAttribute()
    {
        if ($this->expired_at->lte(Carbon::now())) {
            return true;
        }
        return false;
    }
}
