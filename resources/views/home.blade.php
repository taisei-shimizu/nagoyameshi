@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- メッセージ -->
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
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
</div>
@endsection
