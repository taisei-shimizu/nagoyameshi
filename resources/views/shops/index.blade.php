@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    @endcomponent

    <h1>店舗一覧</h1>
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <!-- 検索フォーム -->
    <form method="GET" action="{{ route('shops.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="店名で検索" value="{{ request('name') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-control">
                    <option value="">カテゴリで検索</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-control">
                    <option value="">並び替え</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>価格の安い順</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>価格の高い順</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">検索</button>
            </div>
        </div>
    </form>
    <!-- 検索件数 -->
    <div class="row">
        <div class="col-md-12">
            <p>全 {{ $shops->total() }} 件中 {{ $shops->firstItem() }} 件〜 {{ $shops->lastItem() }} 件を表示</p>
        </div>
    </div>

    <!-- 検索結果 -->
    <div class="row">
        @foreach ($shops as $shop)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <a href="{{ route('shops.show', $shop) }}">
                        <img src="{{ asset($shop->image) }}" class="card-img-top" alt="{{ $shop->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $shop->name }}</h5>
                            <p class="card-text">{{ $shop->description }}</p>
                            <p class="card-text"><strong>予算:</strong> {{ $shop->budget_lower }} - {{ $shop->budget_upper }} 円</p>
                            <p class="card-text">評価: 
                                @if ($shop->average_rating)
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $shop->average_rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                    ({{ number_format($shop->average_rating, 1) }})
                                @else
                                    まだ評価はありません
                                @endif
                            </p>
                            @auth
                                @if (Auth::user()->member_type === 'paid')
                                    @if (Auth::user()->favorites->contains('shop_id', $shop->id))
                                        <form action="{{ route('favorites.destroy', $shop) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-heart text-dark"></i> お気に入り解除
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('favorites.store', $shop) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                <i class="far fa-heart text-light"></i> お気に入り
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center">
        <!-- ページネーションリンクの表示 -->
        {{ $shops->appends(request()->query())->links() }}
    </div>
</div>
@endsection


