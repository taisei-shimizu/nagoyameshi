@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('admin.dashboard'), 'label' => 'ダッシュボード'],
            ['url' => route('admin.categories.index'), 'label' => 'カテゴリ管理'],
            ['url' => route('admin.categories.create'), 'label' => 'カテゴリ作成']
        ])
    @endcomponent

    <h1>新規カテゴリ作成</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">名前</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">説明</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">スラッグ</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" required>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">作成</button>
    </form>
</div>
@endsection
