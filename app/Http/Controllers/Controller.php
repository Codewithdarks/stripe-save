<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    private $url ='http://127.0.0.1:8000';

    public function __construct()
    {
        Stripe::setApiKey('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');
    }

    public function CheckoutSession()
    {
        $stripe = new StripeClient('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');

        $customer = $stripe->customers->create();
        $session = $stripe->checkout->sessions->create([
            'mode' => 'setup',
            'payment_method_types' => ['card'],
            'success_url' => $this->url.'/success?sessionID={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->url.'/cancel',
            'customer' => $customer->id
        ]);
        $stripe->checkout->sessions->retrieve(
          $session->id
        );
        return redirect($session['url']);
    }

    public function CheckoutPayment()
    {
        $stripe = new StripeClient('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');

        $customer = $stripe->customers->create([
            'email' => 'alok@gmail.com'
        ]);
        $payment_method = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 7,
                'exp_year' => 2028,
                'cvc' => '314',
            ],
        ]);
        $stripe->paymentMethods->attach($payment_method->id, ['customer' => $customer->id]);

        $intent = $stripe->paymentIntents->create([
            'amount' => 10000,
            'currency' => 'usd',
            'customer' => $customer->id,
            'payment_method' => $payment_method->id,
            'confirm' => true,
            'setup_future_usage' => 'off_session',
        ]);

        $product = $stripe->products->create([
            'name' => 'pajamas'
        ]);
         $price =  $stripe->prices->create([
            'unit_amount' => 10000,
            'currency' => 'usd',

            'product' => $product->id,
        ]);

        $Session = $stripe->checkout->sessions->create([
            'customer' => $customer->id,
            'success_url' => $this->url.'/success?sessionID={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this.'/cancel',
            'line_items' => [[
                'price_data' => [
                    'currency' => $price->currency,
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' =>$price->unit_amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
        ]);
        return redirect($Session['url']);
    }

    public function StartIntent() {
        $stripe = new StripeClient('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');
//        $customer = $stripe->customers->create([
//            'email' => 'alok@gmail.com'
//        ]);
//        dd($customer);
        $customer = 'cus_M7gVFGh4LUXNFa';
        $payment_method = 'pm_1LPR9DSCROyfBwpuPSNCgSDc';
        $methods = $stripe->paymentMethods->all([
            'customer' => $customer,
            'type' => 'card'
        ]);
        dd($methods);

//        $payment_method = $stripe->paymentMethods->create([
//            'type' => 'card',
//            'card' => [
//                'number' => '4242424242424242',
//                'exp_month' => 7,
//                'exp_year' => 2028,
//                'cvc' => '314',
//            ],
//        ]);
//
//        dd($payment_method);

//        $intent = PaymentIntent::create([
//            'payment_method_types' => ['card'],
//            'amount' => 1099,
//            'currency' => 'usd',
//            'customer' => $customer,
//            'payment_method' => $payment_method,
//            'setup_future_usage' => 'off_session',
//        ]);

        $product = $stripe->products->create([
            'name' => 'pajamas'
        ]);

        $price =  $stripe->prices->create([
            'unit_amount' => 10000,
            'currency' => 'usd',

            'product' => $product->id,
        ]);

        $Session = $stripe->checkout->sessions->create([
            'customer' => $customer,
            'success_url' => url('success'),
            'cancel_url' => url('failed'),
            'line_items' => [[
                'price_data' => [
                    'currency' => $price->currency,
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' =>$price->unit_amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
        ]);

        dd($Session);
    }

    public function SuccessIntent(Request $request) {
        dd($request);
    }

}
