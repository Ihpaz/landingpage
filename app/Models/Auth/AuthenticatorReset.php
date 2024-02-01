<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticatorReset extends Model
{
    use HasFactory;

    protected $table = 'cms_authenticator_resets';
}
