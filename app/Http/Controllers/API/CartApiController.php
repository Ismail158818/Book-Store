<?php

namespace App\Http\Controllers\Api;
use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartUser;

class CartApiController extends Controller
{
    
        public function add_cart(Request $request)
        {
            $user = auth()->user();
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $book = Book::findOrFail($request->input('book_id'));
            $quantity = $request->input('quantity');
            $total_price = $book->book_price * $quantity;
    
            $cartItem = CartUser::create([
                'cart_id' => $cart->id,
                'book_id' => $book->id,
                'quantity' => $quantity,
                'total_price' => $total_price,
            ]);
    
            return response()->json(['status' => 'success', 'message' => 'Item added to cart']);
        }
    
        



        public function view_cart(Request $request)
        {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->with('cartItems.book')->first();
        
            if (!$cart) {
                return response()->json(['status' => 'error', 'message' => 'Cart not found'], 404);
            }
        
            $cartItems = CartUser::where('cart_id', $cart->id)->where('status',0)->with('book')->get();
            return response()->json(['cart' => $cart, 'cartItems' => $cartItems]);
        }
        public function view_cart_price(Request $request)
        {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->with('cartItems.book')->first();
        
            if (!$cart) {
                return response()->json(['status' => 'error', 'message' => 'Cart not found'], 404);
            }
        
            $total_price = 0;
            $cartItems = CartUser::where('cart_id', $cart->id)->with('book')->get();
        
            foreach($cartItems as $cartItem) {
                $total_price += $cartItem->total_price;
            }
        
            return response()->json(['name' => $user->name,'total_price' => $total_price]);
        }
        

}
