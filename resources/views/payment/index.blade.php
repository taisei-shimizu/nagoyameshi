@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('mypage'), 'label' => 'マイページ'],
            ['url' => route('payment.index'), 'label' => '有料プラン']
        ])
    @endcomponent
    <h1>有料プラン登録</h1>
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

    <div class="plan-info mb-4 p-3 bg-light rounded">
        <h2 class="h5 mb-3">有料プランについて</h2>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">300円/月（税込）</h5>
                <!-- <ul class="list-unstyled">
                    <li><i class="bi bi-check-circle"></i> お気に入り登録</li>
                    <li><i class="bi bi-check-circle"></i> レビュー</li>
                    <li><i class="bi bi-check-circle"></i> 予約</li>
                </ul> -->
                <p class="card-text text-muted">お気に入り登録・レビュー・予約機能が使えます。</p>
                <p class="card-text text-muted small">※更新日時までに解約手続きを行わない場合、プランは自動更新されます。</p>
            </div>
        </div>
    </div>

    <form action="{{ route('payment.store') }}" method="POST" id="payment-form">
        @csrf
        <div class="mb-3">
            <label for="card-number" class="form-label">カード番号</label>
            <div id="card-number-element" class="form-control"></div>
        </div>
        <div class="mb-3">
            <label for="card-expiry" class="form-label">有効期限</label>
            <div id="card-expiry-element" class="form-control"></div>
        </div>
        <div class="mb-3">
            <label for="card-cvc" class="form-label">セキュリティコード</label>
            <div id="card-cvc-element" class="form-control"></div>
        </div>
        <button type="submit" class="btn btn-primary" id="card-button" data-secret="{{ $user->createSetupIntent()->client_secret }}">登録</button>
    </form>
</div>
<!-- ローディング画面 -->
<div id="loading-overlay" class="d-none">
    <div class="loading-spinner">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>処理中です。しばらくお待ちください...</p>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stripe = Stripe('{{ env('STRIPE_KEY') }}');
            const elements = stripe.elements();

            const style = {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            const cardNumber = elements.create('cardNumber', { style: style });
            cardNumber.mount('#card-number-element');

            const cardExpiry = elements.create('cardExpiry', { style: style });
            cardExpiry.mount('#card-expiry-element');

            const cardCvc = elements.create('cardCvc', { style: style });
            cardCvc.mount('#card-cvc-element');

            const form = document.getElementById('payment-form');

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                showLoader();

                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardNumber,
                });

                if (error) {
                    console.error(error);
                    document.querySelector('.alert-danger').textContent = error.message;
                    document.querySelector('.alert-danger').classList.remove('d-none');
                } else {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'paymentMethod');
                    hiddenInput.setAttribute('value', paymentMethod.id);
                    form.appendChild(hiddenInput);

                    form.submit();
                }
            });
            function showLoader() {
                document.getElementById('loading-overlay').classList.remove('d-none');
            }
            function hideLoader() {
                document.getElementById('loading-overlay').classList.add('d-none');
            }
        });
    </script>
@endpush
