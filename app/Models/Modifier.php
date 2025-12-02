<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modifier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
    ];

    public function scopeFilter($query)
    {
        if (request('searchName')) {
            $query->where('name', 'like', '%' . request('searchName') . '%');
        }

        return $query;
    }
}
