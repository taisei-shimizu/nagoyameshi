<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'description', 'slug'];
    
    // 店舗とのリレーションを定義
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    // スコープの定義
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }
}
