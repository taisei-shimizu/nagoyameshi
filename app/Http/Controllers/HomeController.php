<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class HomeController extends Controller{
    public function index()
    {
        $shops = Shop::take(12)->get(); // 12件の店舗を取得
        return view('home', compact('shops'));
    }
}

