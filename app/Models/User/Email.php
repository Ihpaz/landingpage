<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'user_emails';

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
