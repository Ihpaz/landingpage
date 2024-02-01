<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimestampDiffHuman
{
    public function getHumanCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getHumanUpdatedAtAttribute()
    {
        return $this->updated_at->diffForHumans();
    }
}