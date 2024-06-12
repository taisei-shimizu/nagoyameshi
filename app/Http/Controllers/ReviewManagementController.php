<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query();

        if ($request->filled('shop_name')) {
            $query->whereHas('shop', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('shop_name') . '%');
            });
        }

        if ($request->filled('user_email')) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%' . $request->input('user_email') . '%');
            });
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', $request->input('is_published'));
        }

        $total = $query->count(); // 総件数の取得
        $reviews = $query->paginate(15);
        $averageScore = $query->average('score'); // 平均評価の取得

        return view('admin.reviews.index', compact('reviews', 'total', 'averageScore'));
    }
    // レビューの公開状態を切り替える
    public function togglePublish($id)
    {
        $review = Review::findOrFail($id);
        $review->is_published = !$review->is_published;
        $review->save();

        return redirect()->route('admin.reviews.index')->with('message', 'レビューの公開状態を変更しました。');
    }
}
