<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Request $request, Shop $shop)
    {
        $user = Auth::user();

        if ($user->favorites()->where('shop_id', $shop->id)->exists()) {
            return response()->json(['message' => 'お気に入り登録済みです。'], 409); 
        }

        Favorite::create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'お気に入りに追加しました。']);
    }

    public function destroy(Shop $shop)
    {
        $user = Auth::user();
        $favorite = $user->favorites()->where('shop_id', $shop->id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'お気に入りを解除しました。']);
        }

        return response()->json(['message' => 'お気に入り登録がされていません。'], 404); 
    }

}
