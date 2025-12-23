<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'eng_name',
        'mm_name',
    ];

    public function scopeFilter(Builder $query)
    {
        if (request('searchName')) {
            $query->where('eng_name', 'like', '%'. request('searchName'). '%')
                ->orwhere('mm_name', 'like', '%'. request('searchName'). '%');
        }
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
