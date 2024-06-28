<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Shop $shop)
    {
        $review = new Review([
            'score' => $request->score,
            'content' => $request->content,
            'shop_id' => $shop->id,
            'user_id' => Auth::id(),
        ]);

        $review->save();

        return redirect()->route('shops.show', $shop)->with('message', 'レビューを投稿しました。');
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        if (Auth::id() !== $review->user_id) {
            return redirect()->back()->with('error', '編集する権限がありません。');
        }

        $review->update([
            'score' => $request->score,
            'content' => $request->content,
        ]);

        return redirect()->route('shops.show', $review->shop_id)->with('message', 'レビューを更新しました。');
    }

    public function destroy(Review $review)
    {
        if (Auth::id() !== $review->user_id) {
            return redirect()->back()->with('error', '削除する権限がありません。');
        }

        $review->delete();

        return redirect()->back()->with('message', 'レビューを削除しました。');
    }
}
