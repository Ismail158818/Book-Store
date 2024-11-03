<?php
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\TypeApiController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\PaymentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

  

    Route::controller(TypeApiController::class)->group(function () {
        Route::post('add-type', 'add_type');
    });

    Route::controller(BookApiController::class)->group(function () {
        Route::post('add-book', 'add_book');
        Route::get('show-all-book', 'show_all_book');
        Route::post('delete-book', 'delete_book');
        Route::post('edit-book', 'edit_book');
    });

    Route::controller(CartApiController::class)->group(function () {
        Route::post('add-cart', 'add_cart');
        Route::post('view-cart', 'view_cart');
        Route::post('view-cart-price', 'view_cart_price');
    });
    Route::controller(PaymentApiController::class)->group(function () {
        Route::post('/handle-payment', 'handlePayment');
    });
   
    
});
Route::controller(AuthApiController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
})->middleware('auth:sactumyy');
