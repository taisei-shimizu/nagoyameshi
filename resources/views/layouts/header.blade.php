<header class="p-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            @guest
                <h1><a href="{{ url('/') }}" class="text-decoration-none">{{ config('app.name', 'Laravel') }}</a></h1>
            @else
                @if (Auth::user()->role === 'admin')
                    <h1><a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none">{{ config('app.name', 'Laravel') }}</a></h1>
                @else
                    <h1><a href="{{ url('/shops') }}" class="text-white text-decoration-none">{{ config('app.name', 'Laravel') }}</a></h1>
                @endif
            @endguest
            <nav>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-primary">新規登録</a>
                    <a href="{{ route('login') }}" class="btn btn-primary">ログイン</a>
                    @if (Route::currentRouteName() !== 'company')
                        <a href="{{ route('company') }}" class="btn btn-primary">運営会社情報</a>
                    @endif
                @else
                    @if (Auth::user()->role !== 'admin')
                        @if (Route::currentRouteName() == 'home')
                            <a href="{{ route('shops.index') }}" class="btn btn-primary">店舗一覧</a>
                        @endif
                            <a href="{{ route('mypage') }}" class="btn btn-primary">マイページ</a>
                    @endif
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="btn btn-primary">ログアウト</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endguest
            
            </nav>
        </div>
    </div>
</header>
