<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'cms_calendar';
    protected $fillable = [
        'date', 'description', 'is_active', 'is_holiday', 'is_weekend'
    ];

    public function scopeFilterHoliday($filter)
    {
        return $filter->where('is_active', true)->where('is_holiday', true);
    }
}
