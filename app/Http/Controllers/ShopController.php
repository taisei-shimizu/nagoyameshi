<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Category;
use App\Helpers\TimeSlotHelper;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Shop::query();
        // 店名での検索
        if ($request->filled('name')) {
            $query->byName($request->input('name'));
        }

        // カテゴリでの検索
        if ($request->filled('category_id')) {
            $query->byCategoryId($request->input('category_id'));
        }

        // 価格順での並び替え
        if ($request->filled('sort')) {
            if ($request->input('sort') == 'price_asc') {
                $query->orderBy('budget_lower', 'asc');
            } elseif ($request->input('sort') == 'price_desc') {
                $query->orderBy('budget_lower', 'desc');
            }
        }
        $shops = $query->paginate(15);
        foreach ($shops as $shop) {
            $shop->average_rating = $shop->reviews()->where('is_published', true)->avg('score');
        }
        $categories = Category::all();
        $breadcrumbs = [
            ['url' => route('shops.index'), 'label' => '名古屋飯店一覧'],
        ];

        return view('shops.index', compact('shops', 'categories', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Shop $shop)
    {
        $breadcrumbs = [
            ['url' => route('shops.index'), 'label' => '名古屋飯店一覧'],
            ['url' => route('shops.show', $shop->id), 'label' => $shop->name],
        ];
        $reviews = $shop->reviews()->where('is_published', true)->get();

        $timeSlots = TimeSlotHelper::getTimeSlots($shop);
        // 平均評価を計算
        $averageRating = $shop->reviews()->where('is_published', true)->avg('score');
        return view('shops.show', compact('shop', 'breadcrumbs', 'reviews', 'timeSlots', 'averageRating'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function edit(Shop $shop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $shop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        //
    }
}
