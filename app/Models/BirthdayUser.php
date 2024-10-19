<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'join_date',
        'birthday',
        'is_wish_sent',
        'is_video_generated',
        'video_url'
    ];

    public function scopeNextWeekBirthdayUsers($query)
    {
        return $query->whereBetween('birthday', [now()->addDays(7), now()->addDays(14)]);
    }

    public function scopeTodayBirthdayUsers($query)
    {
        return $query->whereDate('birthday', now());
    }

    public function scopeThisWeekBirthdayUsers($query)
    {
        return $query->whereBetween('birthday', [now(), now()->addDays(7)]);
    }
}
