<?php

namespace App\Traits;

trait HasAuthorized
{
    public function is_authorized($permission = null) : bool
    {
        if ($this->attributes['user_id'] == auth()->user()->id) {
            return true;
        } elseif (auth()->user()->hasRole('superadmin')) {
            return true;
        } elseif (auth()->user()->can($permission)) {
            return true;
        }
        return false;
    }
}