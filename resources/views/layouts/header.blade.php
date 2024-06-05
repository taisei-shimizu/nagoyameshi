<header class="bg-dark text-white p-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1><a href="{{ url('/') }}" class="text-white text-decoration-none">{{ config('app.name', 'Laravel') }}</a></h1>
            <nav>
                <a href="{{ route('register') }}" class="btn btn-primary">新規登録</a>
                <a href="{{ route('login') }}" class="btn btn-primary">ログイン</a>
                <a href="{{ route('company') }}" class="btn btn-primary">運営会社情報</a>
            </nav>
        </div>
    </div>
</header>
