<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishText extends Model
{
    use HasFactory;

    protected $fillable = [
        'wish_1_text_1',
        'wish_1_text_2',
        'wish_1_text_3',
        'wish_2_text_1',
        'wish_2_text_2',
        'wish_2_text_3',
        'wish_3_text_1',
        'wish_3_text_2',
        'wish_3_text_3',
    ];
}
