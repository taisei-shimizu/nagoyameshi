@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1>{{ $user->name }}さんのマイページ</h1>
    <ul class="list-group">
        <li class="list-group-item"><a href="{{route('mypage.edit')}}">会員情報編集</a></li>
        @if ($user->member_type === 'free')
            <li class="list-group-item"><a href="#">有料会員登録</a></li>
        @endif
        @if ($user->member_type === 'paid')
            <li class="list-group-item"><a href="#">有料会員解約</a></li>
            <li class="list-group-item"><a href="#">支払情報編集</a></li>
            <li class="list-group-item"><a href="{{route('mypage.favorites')}}">お気に入り一覧</a></li>
            <li class="list-group-item"><a href="{{route('mypage.reservations')}}">予約一覧</a></li>
        @endif
        <li class="list-group-item"><a href="#">退会</a></li>
    </ul>
</div>
@endsection
