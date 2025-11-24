<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'eng_name',
        'mm_name',
        'price',
        'eng_description',
        'mm_description',
        'image_path',
        'is_available',
    ];
}
