@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('admin.dashboard'), 'label' => 'ダッシュボード'],
            ['url' => route('admin.categories.index'), 'label' => 'カテゴリ管理']
        ])
    @endcomponent

    <h1>カテゴリ一覧</h1>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="category_name" class="form-control" placeholder="カテゴリ名で検索" value="{{ request('category_name') }}">
            <button class="btn btn-outline-secondary" type="submit">検索</button>
        </div>
    </form>

    <p>全 {{ $total }}件中 {{ $categories->firstItem() }}件〜{{ $categories->lastItem() }}件を表示</p>

    <div class="mb-3">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">新規カテゴリ作成</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>説明</th>
                <th>スラッグ</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">編集</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('本当に削除しますか？')"; >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $categories->appends(request()->query())->links() }}
    </div>
</div>
@endsection
