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
    // إعداد مفتاح API الخاص بـ Stripe باستخدام المفتاح المخزن في ملف البيئة .env
    Stripe::setApiKey(env('STRIPE_SECRET'));

    // الحصول على المستخدم الذي قام بتسجيل الدخول حاليًا
    $user = auth()->user();

    // جلب سلة المشتريات الخاصة بالمستخدم، مع العناصر التي تحتويها (والكتب المرتبطة بكل عنصر)
    $cart = Cart::where('user_id', $user->id)->with('cartItems.book')->findOrFail($request->input('cart_id'));

    // حساب السعر الإجمالي لجميع العناصر في السلة
    $total_price = 0;
    foreach ($cart->cartItems as $cartItem) {
        $total_price += $cartItem->total_price; // جمع سعر كل عنصر في السلة
    }

    // إنشاء طلب دفع باستخدام Stripe
    $paymentIntent = PaymentIntent::create([
        'amount' => $total_price * 100, // تحويل السعر الإجمالي إلى سنتات لأن Stripe يتطلب ذلك
        'currency' => 'usd', // العملة المستخدمة (الدولار الأمريكي)
        'payment_method' => $request->input('payment_method'), // طريقة الدفع المدخلة في الطلب
        'confirmation_method' => 'automatic', // طريقة التأكيد تلقائية
        'confirm' => true, // تأكيد الدفع فورًا
        'return_url' => 'https://your-return-url.com', // عنوان URL للعودة بعد الدفع
        'metadata' => [
            'cart_id' => $cart->id, // تخزين معرف السلة كبيانات إضافية
            'user_id' => $user->id, // تخزين معرف المستخدم كبيانات إضافية
        ],
    ]);

    // التحقق مما إذا كانت عملية الدفع ناجحة
    if ($paymentIntent->status == 'succeeded') {
        // إذا نجحت العملية، حذف العلاقة بين المستخدم والسلة
        CartUser::where('cart_id', $cart->id)->update(['status'=>1]);
        // حذف السلة نفسها
        $cart->update(['status' => 'paid']);
        // إعادة استجابة JSON تشير إلى نجاح الدفع وتفريغ السلة
        return response()->json(['status' => 'success', 'message' => 'Payment succeeded and cart emptied']);
    }

    // في حالة فشل الدفع، إعادة استجابة JSON مع رسالة خطأ
    return response()->json(['status' => 'error', 'message' => 'Payment failed'], 400);
}

}
