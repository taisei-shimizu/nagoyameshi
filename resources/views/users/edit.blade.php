@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
        @slot('breadcrumbs', [
            ['url' => route('mypage'), 'label' => 'マイページ'],
            ['url' => route('mypage.edit'), 'label' => '会員情報編集']
        ])
    @endcomponent

    <h1>会員情報編集</h1>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('mypage.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">ユーザー名</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">パスワード確認</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('mypage') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>

    <!-- 区切り線 -->
    <hr class="my-5">

    <h2>会員種別</h2>
    <div class="mb-3">
            <label for="member_type" class="form-label">会員種別</label>
            <input type="text" class="form-control" id="member_type" name="member_type" value="{{ $user->member_type === 'paid' ? '有料会員' : '無料会員' }}" disabled>
    </div>
    @if ($user->member_type === 'paid')
        <div class="form-group mt-3">
            <form action="{{ route('payment.cancel') }}" method="POST" onsubmit="return confirm('本当に解約しますか？')">
                @csrf
                <button type="submit" class="btn btn-danger">有料プラン解約</button>
            </form>
        </div>
    @endif

    <!-- 区切り線 -->
    <hr class="my-5">

    <h2>退会</h2>
    <form action="{{ route('users.destroy') }}" method="POST" class="mt-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('本当に退会しますか？')">退会する</button>
    </form>

</div>
@endsection
