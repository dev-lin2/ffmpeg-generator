<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayVideoRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'birthday_user_id',
        'wish_text_1',
        'wish_text_2',
        'wish_text_3',
    ];
}
