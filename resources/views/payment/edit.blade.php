@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('mypage'), 'label' => 'マイページ'],
            ['url' => route('payment.edit'), 'label' => '支払情報編集']
        ])
    @endcomponent
    <h1>支払情報編集</h1>

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

    <form action="{{ route('payment.update') }}" method="POST" id="payment-form">
        @csrf
        @if ($paymentMethod)
            <p>現在のカード: **** **** **** {{ $paymentMethod->card->last4 }}</p>
        @endif
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

        <button type="submit" class="btn btn-primary" id="card-button" data-secret="{{ $user->createSetupIntent()->client_secret }}">更新</button>
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
