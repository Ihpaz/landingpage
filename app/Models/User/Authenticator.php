<?php

namespace App\Models\User;

use App\Models\User;
use App\Traits\HasAuthorized;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authenticator extends Model
{
    use HasFactory, HasAuthorized;

    protected $table = 'user_authenticators';

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
