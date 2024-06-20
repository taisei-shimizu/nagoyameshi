@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('admin.dashboard'), 'label' => 'ダッシュボード'],
            ['url' => route('admin.users.index'), 'label' => '会員管理']
        ])
    @endcomponent

    <h1>会員管理</h1>
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

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="email" class="form-control" placeholder="メールアドレスで検索" value="{{ request('email') }}">
            </div>
            <div class="col-md-3">
                <select name="member_type" class="form-control">
                    <option value="">全ての会員</option>
                    <option value="free" {{ request('member_type') == 'free' ? 'selected' : '' }}>無料会員</option>
                    <option value="paid" {{ request('member_type') == 'paid' ? 'selected' : '' }}>有料会員</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">検索</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.users.export')}}?{{ http_build_query(request()->query()) }}" class="btn btn-success">CSVエクスポート</a>
            </div>
        </div>
    </form>

    <p>全 {{ $total }}件中 {{ $users->firstItem() }}件〜{{ $users->lastItem() }}件を表示</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>会員種別</th>
                <th>メールアドレス</th>
                <th>登録日</th>
                <th>処理</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->member_type === 'paid' ? '有料会員' : '無料会員' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('本当に退会させますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">退会</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection
