@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1>店舗一覧</h1>
    <div class="row">
        @foreach ($shops as $shop)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset($shop->image) }}" class="card-img-top" alt="{{ $shop->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $shop->name }}</h5>
                        <p class="card-text">{{ $shop->description }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center">
        {{ $shops->links() }} <!-- ページネーションリンクの表示 -->
    </div>
</div>
@endsection
