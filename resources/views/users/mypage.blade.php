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
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('mypage'), 'label' => 'マイページ'],
            ['url' => route('mypage.edit'), 'label' => '会員情報編集']
        ])
    @endcomponent

    <h1>{{ $user->name }}さんのマイページ</h1>
    <ul class="list-group">
        <li class="list-group-item"><a href="{{route('mypage.edit')}}">会員情報編集</a></li>
        @if ($user->member_type === 'free')
            <li class="list-group-item"><a href="{{ route('payment.index') }}">有料プラン</a></li>
        @endif
        @if ($user->member_type === 'paid')
            <li class="list-group-item"><a href="{{ route('payment.edit') }}">支払情報編集</a></li>
            <li class="list-group-item"><a href="{{route('mypage.favorites')}}">お気に入り一覧</a></li>
            <li class="list-group-item"><a href="{{route('mypage.reservations')}}">予約一覧</a></li>
        @endif
    </ul>
</div>
@endsection
