<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'employee_id',
        'uniqid',
        'join_date',
        'birthday',
        'is_wish_sent',
        'is_video_generated',
        'video_url',
        'template_video_id',
        'department',
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

    public function templateVideo()
    {
        return $this->belongsTo(TemplateVideo::class , 'template_video_id');
    }
}
