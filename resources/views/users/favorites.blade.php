@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- メッセージ -->
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
    <!-- パンくずリスト -->
    @component('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    @endcomponent

    <h1>お気に入り一覧</h1>

    @if ($favorites->isEmpty())
        <p>お気に入りはありません。</p>
    @else
        <div class="row">
            @foreach ($favorites as $favorite)
                <div class="col-md-4">
                    <div class="card mb-3">
                        <img src="{{ asset($favorite->shop->image) }}" class="card-img-top" alt="{{ $favorite->shop->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $favorite->shop->name }}</h5>
                            <p class="card-text">{{ $favorite->shop->description }}</p>
                            <a href="{{ route('shops.show', $favorite->shop) }}" class="btn btn-primary">詳細を見る</a>
                            <form action="{{ route('favorites.destroy', $favorite->shop) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-heart text-dark"></i> お気に入り解除
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
