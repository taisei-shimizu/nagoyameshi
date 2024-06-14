@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('admin.dashboard'), 'label' => 'ダッシュボード'],
            ['url' => route('admin.shops.index'), 'label' => '店舗管理']
        ])
    @endcomponent
    <h1>店舗管理</h1>

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
    <form action="{{ route('admin.shops.index') }}" method="GET">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="店舗名で検索" value="{{ request('name') }}">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-control">
                    <option value="">カテゴリで検索</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">検索</button>
            </div>
        </div>

        <!-- CSVインポートとエクスポートボタン -->
        <div class="d-flex mb-3">
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                CSVインポート
            </button>
            <a href="{{ route('admin.shops.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-secondary">CSVエクスポート</a>
        </div>
    </form>

    <div id="loading" class="d-none mt-3">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>アップロード中...</p>
    </div>

    <p>全 {{ $total }}件中 {{ $shops->firstItem() }}件〜{{ $shops->lastItem() }}件を表示</p>
    <a href="{{ route('admin.shops.create') }}" class="btn btn-primary mb-3">新規登録</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>画像</th>
                <th>店舗名</th>
                <th>説明</th>
                <th>カテゴリ</th>
                <th>予算</th>
                <th>営業時間</th>
                <th>定休日</th>
                <th>住所</th>
                <th>電話番号</th>
                <th>編集</th>
                <th>削除</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shops as $shop)
                <tr>
                    <td>{{ $shop->id }}</td>
                    <td>
                        <img src="{{ asset($shop->image) }}" alt="{{ $shop->name }}" style="max-width: 100px;">
                    </td>
                    <td>{{ $shop->name }}</td>
                    <td>{{ $shop->description }}</td>
                    <td>{{ $shop->category->name }}</td>
                    <td>{{ $shop->budget_lower }} - {{ $shop->budget_upper }} 円</td>
                    <td>{{ $shop->opening_time }} - {{ $shop->closing_time }}</td>
                    <td>{{ $shop->closed_day }}</td>
                    <td>{{$shop->postal_code}} {{ $shop->address }}</td>
                    <td>{{ $shop->phone }}</td>
                    <td>
                        <a href="{{ route('admin.shops.edit', ['shop' => $shop->id]) }}" class="btn btn-warning">編集</a>
                    </td>
                    <td>
                        <form action="{{ route('admin.shops.destroy', ['shop' => $shop->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('本当に削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $shops->appends(request()->query())->links() }}
    </div>
</div>

<!-- CSVアップロードモーダル -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">CSVアップロード</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.shops.import') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
                    @csrf
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSVファイル</label>
                        <input type="file" name="csv_file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">アップロード</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function showLoader() {
        document.getElementById('loading').classList.remove('d-none');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action="{{ route('admin.shops.import') }}"]');
        form.addEventListener('submit', showLoader);
    });
</script>

@endsection

