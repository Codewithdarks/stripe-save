@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="row">
                    <div class="col shadow-lg p-3 mb-5 bg-body rounded m-5">
                        <form action="{{ route('checkout.session') }}" method="post">@csrf
                            <h4 class="text-center">Set up your Card</h4>
                            <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Set Up</button>
                            </div>
                        </form>
                    </div>
                        <div class="col shadow-lg p-3 mb-5 bg-body rounded m-5">
                        <form action="{{route('checkout.payment')}}" method="post">@csrf
                            <h4 class="text-center">Checkout From here</h4>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Checkout</button>
                            </div>
                        </form>
                        </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
    @section('js')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            var stripe = Stripe('pk_test_51L3a7TSCROyfBwpuSx2LJvMwnJrGnXZqF8bGABBpUURnwTPbtH7fb2bceuUNZlHRk5Q3bTiijpTTmdnpej9DJmXp00DwU7L2Zy');

            var elements = stripe.elements();
            var cardElement = elements.create('card');
            cardElement.mount('#card-element');

            var form = document.getElementById('payment-form');

            var resultContainer = document.getElementById('payment-result');
            cardElement.on('change', function(event) {
                if (event.error) {
                    resultContainer.textContent = event.error.message;
                } else {
                    resultContainer.textContent = '';
                }
            });

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                resultContainer.textContent = "";
                stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                }).then(handlePaymentMethodResult);
            });

            function handlePaymentMethodResult(result) {
                if (result.error) {
                    // An error happened when collecting card details, show it in the payment form
                    resultContainer.textContent = result.error.message;
                } else {
                    // Otherwise send paymentMethod.id to your server (see Step 3)
                    fetch('/pay', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ payment_method_id: result.paymentMethod.id })
                    }).then(function(result) {
                        return result.json();
                    }).then(handleServerResponse);
                }
            }

            function handleServerResponse(responseJson) {
                if (responseJson.error) {
                    // An error happened when charging the card, show it in the payment form
                    resultContainer.textContent = responseJson.error;
                } else {
                    // Show a success message
                    resultContainer.textContent = 'Success!';
                }
            }
        </script>
    @endsection
@endsection
