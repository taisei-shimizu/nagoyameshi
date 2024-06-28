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
                            <button class="btn btn-danger favorite-remove-button" data-id="{{ $favorite->shop->id }}">
                                <i class="fas fa-heart text-dark"></i> お気に入り解除
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.favorite-remove-button');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const shopId = this.getAttribute('data-id');
                const url = `{{ url('shops') }}/${shopId}/favorite`;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ _token: token })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // お気に入り解除成功時の処理
                    this.closest('.col-md-4').remove(); // カードを削除
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
@endsection
