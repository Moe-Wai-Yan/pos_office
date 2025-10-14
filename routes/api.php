<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\RegionController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\API\DeliveryFeeController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ContactDetailController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\VersionSettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//auth
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:api');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('/forgot-password-code/verify', [ForgotPasswordController::class, 'verifyForgotPasswordCode']);
    Route::post('/update-password', [ForgotPasswordController::class, 'updatePassword']);

    //version
    Route::get('version', [VersionSettingController::class, 'index']);

    //banners
    Route::get('banners', [BannerController::class, 'index']);

    //brands
    Route::get('brands', [BrandController::class, 'index']);

    //category
    Route::get('categories', [CategoryController::class, 'index']);

    //sub-categories
    Route::get('/sub-categories/{category_id?}', [SubCategoryController::class, 'subCategoryList'])->where('category_id', '[0-9]+');

    //products
    Route::get('/products', [ProductController::class, 'listing']);
    Route::get('products/{id}', [ProductController::class, 'detail']);
    Route::get('products/{id}/images', [ProductController::class, 'productImages']);
    Route::get('new-arrivals', [ProductController::class, 'newArrivals']);

    //posts
    Route::get('/posts', [PostController::class, 'list']);
    Route::get('/posts/{post}', [PostController::class, 'detail']);
    Route::post('/posts/{post}/comment', [PostController::class, 'commentStore']);
    Route::get('/posts/{post}/comments', [PostController::class, 'comments']);
    Route::get('/comments/{comment}', [PostController::class, 'commentDetail']);
    Route::delete('/comments/{comment}/delete', [PostController::class, 'commentDelete']);

    //contact-detail
    Route::get('contact-detail', [ContactDetailController::class, 'index']);

    //note
    Route::get('notes', [NoteController::class, 'index']);
});

Route::prefix('v1')->middleware(['auth:api', 'bannedCustomerCheck'])->group(function () {
    //customers
    Route::get('customer', [CustomerController::class, 'index']);
    Route::put('customer', [CustomerController::class, 'update']);
    Route::put('customer/update-password', [CustomerController::class, 'updatePassword']);
    Route::delete('customer/delete', [CustomerController::class, 'delete']);

    //regions & cash on delivery
    Route::get('regions', [RegionController::class, 'list']);
    Route::get('regions/{id}', [RegionController::class, 'detail']);

    Route::get('delivery-fees', [DeliveryFeeController::class, 'list']);
    Route::get('delivery-fees/{id}', [DeliveryFeeController::class, 'detail']);
    Route::get('delivery-fees/regions/{regionId}', [DeliveryFeeController::class, 'deliveryFeeByRegion']);

    //payments
    Route::get('payments', [PaymentController::class, 'index']);

    //add to cart
    Route::get('carts', [CartController::class, 'index']);
    Route::post('carts/add', [CartController::class, 'store']);
    Route::post('carts/update', [CartController::class, 'update']);
    Route::post('carts/remove', [CartController::class, 'remove']);
    Route::post('carts/clear', [CartController::class, 'clear']);

    //order
    Route::post('orders', [OrderController::class, 'create']);
    Route::get('orders/{id}', [OrderController::class, 'detail']);
    Route::get('orders', [OrderController::class, 'list']);

    //noti
    Route::get('notifications', [NotificationController::class, 'list']);
    Route::get('notifications/{id}/read', [NotificationController::class, 'read']);

    //wishlist
    Route::get('wishlist', [WishlistController::class, 'list']);
    Route::post('wishlist/change', [WishlistController::class, 'change']);
});
