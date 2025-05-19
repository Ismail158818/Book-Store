<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Cart;
use App\Models\CartUser;

class PaymentApiController extends Controller
{
    public function handlePayment(Request $request)
{
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $user = auth()->user();

    $cart = Cart::where('user_id', $user->id)->with('cartItems.book')->findOrFail($request->input('cart_id'));

    $total_price = 0;
    foreach ($cart->cartItems as $cartItem) {
        $total_price += $cartItem->total_price; 
    }

    // إنشاء طلب دفع باستخدام Stripe
    $paymentIntent = PaymentIntent::create([
        'amount' => $total_price * 100, 
        'currency' => 'usd', 
        'payment_method' => $request->input('payment_method'),
        'confirmation_method' => 'automatic', 
        'confirm' => true, // تأكيد الدفع فورًا
        'return_url' => 'https://your-return-url.com', 
        'metadata' => [
            'cart_id' => $cart->id, 
            'user_id' => $user->id, 
        ],
    ]);

    if ($paymentIntent->status == 'succeeded') {
        CartUser::where('cart_id', $cart->id)->update(['status'=>1]);
        $cart->update(['status' => 'paid']);
        return response()->json(['status' => 'success', 'message' => 'Payment succeeded and cart emptied']);
    }

    return response()->json(['status' => 'error', 'message' => 'Payment failed'], 400);
}

}
