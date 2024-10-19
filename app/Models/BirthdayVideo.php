<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'birthday_user_id',
        'template_video_id',
        'name',
        'video_url',
    ];
}
