<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'category_id',
        'budget_lower',
        'budget_upper',
        'opening_time',
        'closing_time',
        'closed_day',
        'postal_code',
        'address',
        'phone'
    ];

    // カテゴリーとのリレーションを定義
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
