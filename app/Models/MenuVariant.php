<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'price',
        'is_available',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function scopeFilter(Builder $query)
    {
        if (request('searchName')) {
            $query->where('name', 'like', '%' . request('searchName') . '%')
                ->orWhereHas('menu', function($q) {
                    $q->where('eng_name', 'like', '%' . request('searchName') . '%')
                    ->orWhere('mm_name', 'like', '%' . request('searchName') . '%');
                });
        }
    }
}
