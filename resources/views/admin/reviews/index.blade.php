@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('admin.dashboard'), 'label' => 'ダッシュボード'],
            ['url' => route('admin.reviews.index'), 'label' => 'レビュー管理']
        ])
    @endcomponent

    <h1>レビュー管理</h1>
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.reviews.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="shop_name" class="form-control" placeholder="店舗名で検索" value="{{ request('shop_name') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="user_email" class="form-control" placeholder="会員のメールアドレスで検索" value="{{ request('user_email') }}">
            </div>
            <div class="col-md-2">
                <select name="is_published" class="form-control">
                    <option value="">公開状態で検索</option>
                    <option value="1" {{ request('is_published') == '1' ? 'selected' : '' }}>公開</option>
                    <option value="0" {{ request('is_published') == '0' ? 'selected' : '' }}>非公開</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">検索</button>
            </div>
        </div>
    </form>

    <p>全 {{ $total }}件中 {{ $reviews->firstItem() }}件〜{{ $reviews->lastItem() }}件を表示</p>

    @if ($total > 0)
        <div class="alert alert-info">
            <strong>平均評価: {{ number_format($averageScore, 2) }}</strong>
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>店舗名</th>
                <th>会員のメールアドレス</th>
                <th>評価</th>
                <th>レビュー内容</th>
                <th>投稿日</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->shop->name }}</td>
                    <td>{{ $review->user->email }}</td>
                    <td>{{ $review->score }}</td>
                    <td>{{ $review->content }}</td>
                    <td>{{ $review->created_at->format('Y-m-d') }}</td>
                    <td>
                        <form action="{{ route('admin.reviews.togglePublish', $review->id) }}" method="POST" onsubmit="return confirm('本当に変更しますか？');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $review->is_published ? 'btn-success' : 'btn-danger' }}">
                                {{ $review->is_published ?'公開中' : '非公開' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $reviews->appends(request()->query())->links() }}
    </div>
</div>
@endsection
