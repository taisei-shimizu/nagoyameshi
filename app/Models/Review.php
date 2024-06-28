<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
        'content',
        'is_published',
        'shop_id',
        'user_id',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // スコープを定義
    public function scopeByShopName($query, $shopName)
    {
        return $query->whereHas('shop', function ($query) use ($shopName) {
            $query->where('name', 'like', '%' . $shopName . '%');
        });
    }

    public function scopeByUserEmail($query, $userEmail)
    {
        return $query->whereHas('user', function ($query) use ($userEmail) {
            $query->where('email', 'like', '%' . $userEmail . '%');
        });
    }

    public function scopePublished($query, $isPublished)
    {
        return $query->where('is_published', $isPublished);
    }
}
