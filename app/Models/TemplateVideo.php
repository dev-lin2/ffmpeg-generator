<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'wish_text_1',
        'wish_text_2',
        'video_url',
    ];
}
