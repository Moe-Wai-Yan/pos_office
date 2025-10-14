<?php

use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\UserController;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthContoller;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\Backend\NoteController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\RegionController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\VariationController;
use App\Http\Controllers\Backend\WarehouseController;
use App\Http\Controllers\Backend\WholesaleController;
use App\Http\Controllers\Other\ApplicationController;
use App\Http\Controllers\Backend\DeliveryFeeController;
use App\Http\Controllers\Backend\SubCategoryController;
use App\Http\Controllers\OrderSuccessMessageController;
use App\Http\Controllers\Backend\ContactDetailController;
use App\Http\Controllers\Backend\VersionSettingController;
use App\Http\Controllers\Backend\UserWarehousePermissionController;

Route::get('/',function(){
    if(Auth::check()){
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

Route::get('/privacy-policy',function(){
    return view('privacy-policy');
})->name('privacyPolicy');

//Auth
Route::get('/2023',[AuthContoller::class,'login'])->name('login');
Route::post('/2023',[AuthContoller::class,'postLogin'])->name('postLogin');

Route::get('/logout',[AuthContoller::class,'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //role crud
      Route::resource('roles', RoleController::class);

      //warehouse crud
      Route::resource('warehouses',WarehouseController::class);

      //User warehouse Permission crud
      Route::resource('user-warehouse-permissions',UserWarehousePermissionController::class);

      //User crud
      Route::resource('users',UserController::class);

      //Supplier Crud
      Route::resource('suppliers',SupplierController::class);



    //profile
    Route::get('/edit-profile',[AuthContoller::class,'editProfile'])->name('profile.edit');
    Route::post('/edit-profile',[AuthContoller::class,'updateProfile'])->name('profile.update');

    //auth
    Route::get('/edit-password',[AuthContoller::class,'editPassword'])->name('editPassword');
    Route::post('/edit-password',[AuthContoller::class,'updatePassword'])->name('updatePassword');

    //product colors
    Route::get('/products/colors',[ProductColorController::class,'index'])->name('product.color');
    Route::get('/products/colors/datatable/ssd', [ProductColorController::class, 'serverSide']);

    Route::get('/products/colors/create',[ProductColorController::class,'create'])->name('product.color.create');
    Route::post('/products/colors/create',[ProductColorController::class,'store'])->name('product.color.store');
    Route::get('/products/colors/edit/{product_color}',[ProductColorController::class,'edit'])->name('product.color.edit');
    Route::put('/products/colors/edit/{product_color}',[ProductColorController::class,'update'])->name('product.color.update');
    Route::delete('/products/colors/{product_color}',[ProductColorController::class,'destroy'])->name('product.color.destroy');

    //product sizes
    Route::get('/products/sizes',[ProductSizeController::class,'index'])->name('product.size');
    Route::get('/products/sizes/datatable/ssd', [ProductSizeController::class, 'serverSide']);

    Route::get('/products/sizes/create',[ProductSizeController::class,'create'])->name('product.size.create');
    Route::post('/products/sizes/create',[ProductSizeController::class,'store'])->name('product.size.store');
    Route::get('/products/sizes/edit/{product_size}',[ProductSizeController::class,'edit'])->name('product.size.edit');
    Route::put('/products/sizes/edit/{product_size}',[ProductSizeController::class,'update'])->name('product.size.update');
    Route::delete('/products/sizes/{product_size}',[ProductSizeController::class,'destroy'])->name('product.size.destroy');

    //Product Variation
    Route::get('/variations', [VariationController::class, 'index'])->name('variation');
    Route::get('/variations/datatable/ssd', [VariationController::class, 'serverSide']);

    Route::get('/variations/create', [VariationController::class, 'create'])->name('variation.create');
    Route::post('/variations', [VariationController::class, 'store'])->name('variation.store');
    Route::get('/variations/{variation}', [VariationController::class, 'detail'])->name('variation.detail');
    Route::get('/variations/{variation}/edit', [VariationController::class, 'edit'])->name('variation.edit');
    Route::put('/variations/{variation}/update', [VariationController::class, 'update'])->name('variation.update');
    Route::delete('/variations/{variation}', [VariationController::class, 'destroy'])->name('variation.destroy');

    Route::post('/variations/{variation}/types', [VariationController::class, 'getTypes']);
    //Products
    Route::get('/products', [ProductController::class, 'listing'])->name('product');
    Route::get('/products/datatable/ssd', [ProductController::class, 'serverSide']);

    Route::get('/products/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::get('/products/{product}', [ProductController::class, 'detail'])->name('product.detail');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/products/{product}/update', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

    Route::get('product-images/{product}', [ProductController::class, 'images']); // get images from edit

    //Wholesales
    Route::get('/wholesales', [WholesaleController::class, 'index'])->name('wholesale');
    Route::get('/wholesales/datatable/ssd', [WholesaleController::class, 'serverSide']);

    Route::get('/wholesales/create', [WholesaleController::class, 'create'])->name('wholesale.create');
    Route::post('/wholesales', [WholesaleController::class, 'store'])->name('wholesale.store');
    Route::get('/wholesales/{wholesale}', [WholesaleController::class, 'detail'])->name('wholesale.detail');
    Route::get('/wholesales/{wholesale}/edit', [WholesaleController::class, 'edit'])->name('wholesale.edit');
    Route::put('/wholesales/{wholesale}/update', [WholesaleController::class, 'update'])->name('wholesale.update');
    Route::delete('/wholesales/{wholesale}', [WholesaleController::class, 'destroy'])->name('wholesale.destroy');

    Route::get('/products/{product}/variations', [ProductController::class, 'fetchVariations']);
    Route::get('/variations/{id}/options', [ProductController::class, 'fetchVariationOptions']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('category');
    Route::get('/categories/datatable/ssd', [CategoryController::class, 'serverSide']);

    Route::get('/categories/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/categories/{category}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::get('/categories/{category}/subcategories', [CategoryController::class, 'subcategories']);

    //subCategories
    Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('subcategory');
    Route::get('/sub-categories/datatable/ssd', [SubCategoryController::class, 'serverSide']);

    Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('subcategory.create');
    Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('subcategory.store');
    Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('subcategory.edit');
    Route::put('/sub-categories/{subCategory}/update', [SubCategoryController::class, 'update'])->name('subcategory.update');
    Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('subcategory.destroy');

    // Brands
    Route::get('/brands', [BrandController::class, 'index'])->name('brand');
    Route::get('/brands/datatable/ssd', [BrandController::class, 'serverSide']);

    Route::get('/brands/create', [BrandController::class, 'create'])->name('brand.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brand.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brand.edit');
    Route::put('/brands/{brand}/update', [BrandController::class, 'update'])->name('brand.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');

    //banners
    Route::get('/banners',[BannerController::class,'index'])->name('banner');
    Route::get('/banners/datatable/ssd', [BannerController::class, 'serverSide']);

    Route::get('/banners/create',[BannerController::class,'create'])->name('banner.create');
    Route::post('/banners/create',[BannerController::class,'store'])->name('banner.store');
    Route::get('/banners/edit/{banner}',[BannerController::class,'edit'])->name('banner.edit');
    Route::post('/banners/edit/{banner}',[BannerController::class,'update'])->name('banner.update');
    Route::delete('/banners/{banner}',[BannerController::class,'destroy'])->name('banner.destroy');

    //payments
    Route::get('/payments',[PaymentController::class,'index'])->name('payment');
    Route::get('/payments/datatable/ssd', [PaymentController::class, 'serverSide']);

    Route::get('/payments/create',[PaymentController::class,'create'])->name('payment.create');
    Route::post('/payments/create',[PaymentController::class,'store'])->name('payment.store');
    Route::get('/payments/edit/{payment}',[PaymentController::class,'edit'])->name('payment.edit');
    Route::post('/payments/edit/{payment}',[PaymentController::class,'update'])->name('payment.update');
    Route::delete('/payments/{payment}',[PaymentController::class,'destroy'])->name('payment.destroy');

    //customers
    Route::get('/customers',[CustomerController::class,'index'])->name('customer');
    Route::get('/customers/{customer}',[CustomerController::class,'detail'])->name('customer.detail');
    Route::delete('/customers/{customer}',[CustomerController::class,'destroy'])->name('customer.destroy');
    Route::get('/customers/edit/{customer}',[CustomerController::class,'edit'])->name('customer.edit');
    Route::put('/customers/edit/{customer}',[CustomerController::class,'update'])->name('customer.update');
    Route::put('/customers/update-password/{customer}',[CustomerController::class,'updatePassword'])->name('customer.updatePassword');
    Route::post('/customers/ban/{customer}',[CustomerController::class,'banCustomer'])->name('customer.ban');

    Route::get('/customers/datatable/ssd', [CustomerController::class, 'serverSide']);

    //regions (cash on delivery)
    Route::get('/regions',[RegionController::class,'index'])->name('region');
    Route::get('/regions/datatable/ssd', [RegionController::class, 'serverSide']);

    Route::get('/regions/create',[RegionController::class,'create'])->name('region.create');
    Route::post('/regions/create',[RegionController::class,'store'])->name('region.store');
    Route::get('/regions/edit/{region}',[RegionController::class,'edit'])->name('region.edit');
    Route::post('/regions/edit/{region}',[RegionController::class,'update'])->name('region.update');
    Route::delete('/regions/{region}',[RegionController::class,'destroy'])->name('region.destroy');

    //delivery fee
    Route::get('/delivery-fees',[DeliveryFeeController::class,'index'])->name('deliveryfee');
    Route::get('/delivery-fees/datatable/ssd', [DeliveryFeeController::class, 'serverSide']);
    Route::get('/delivery-fees/create',[DeliveryFeeController::class,'create'])->name('deliveryfee.create');
    Route::post('/delivery-fees/create',[DeliveryFeeController::class,'store'])->name('deliveryfee.store');
    Route::get('/delivery-fees/edit/{delivery_fee}',[DeliveryFeeController::class,'edit'])->name('deliveryfee.edit');
    Route::post('/delivery-fees/edit/{delivery_fee}',[DeliveryFeeController::class,'update'])->name('deliveryfee.update');
    Route::delete('/delivery-fees/{delivery_fee}',[DeliveryFeeController::class,'destroy'])->name('deliveryfee.destroy');

    //Version Setting
    Route::get('/version-setting',[VersionSettingController::class,'index'])->name('versionSetting');
    Route::get('/version-setting/datatable/ssd', [VersionSettingController::class, 'serverSide']);
    Route::get('/version-setting/edit/{id}',[VersionSettingController::class,'edit'])->name('versionSetting.edit');
    Route::post('/version-setting/edit/{id}',[VersionSettingController::class,'update'])->name('versionSetting.update');

    //orders
    Route::get('/orders',[OrderController::class,'index'])->name('order');
    Route::get('/orders/status/{status}',[OrderController::class,'orderByStatus'])->name('orderByStatus');
    Route::delete('/orders/{order}',[OrderController::class,'destroy'])->name('order.destroy');

    Route::post('/orders/{order}',[OrderController::class,'updateStatus'])->name('order.updateStatus');
    Route::get('/orders/cancel/{order}',[OrderController::class,'cancelOrder'])->name('order.cancel');
    Route::post('/orders/cancel/{order}',[OrderController::class,'saveCancelOrder'])->name('order.saveCancel');

    Route::get('/orders/refund/all',[OrderController::class,'refundOrderList'])->name('order.refund.list');
    Route::get('/orders/refund/{order}',[OrderController::class,'refundOrder'])->name('order.refund');
    Route::post('/orders/refund/{order}',[OrderController::class,'saveRefundOrder'])->name('order.saveRefund');

    Route::get('/orders/{order}/{notiId?}',[OrderController::class,'detail'])->name('order.detail');

    Route::get('/all-orders/datatable/ssd', [OrderController::class, 'getAllOrder']);
    Route::get('/refund-orders/datatable/ssd',[OrderController::class,'getRefundList']);
    Route::get('/orders/{status}/datatable/ssd',[OrderController::class,'getOrderByStatus']);

    //order success message
    Route::get('/order-success-messages',[OrderSuccessMessageController::class,'index'])->name('orderSuccessMessage');
    Route::get('/order-success-messages/datatable/ssd', [OrderSuccessMessageController::class, 'serverSide']);

    Route::get('/order-success-messages/create',[OrderSuccessMessageController::class,'create'])->name('orderSuccessMessage.create');
    Route::post('/order-success-messages/create',[OrderSuccessMessageController::class,'store'])->name('orderSuccessMessage.store');
    Route::get('/order-success-messages/edit/{order_success_message}',[OrderSuccessMessageController::class,'edit'])->name('orderSuccessMessage.edit');
    Route::post('/order-success-messages/edit/{order_success_message}',[OrderSuccessMessageController::class,'update'])->name('orderSuccessMessage.update');
    Route::delete('/order-success-messages/{order_success_message}',[OrderSuccessMessageController::class,'destroy'])->name('orderSuccessMessage.destroy');

    //post
    Route::get('/posts', [PostController::class, 'index'])->name('post');
    Route::get('/posts/datatable/ssd', [PostController::class, 'serverSide']);
    Route::get('/posts/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/posts/create', [PostController::class, 'store'])->name('post.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::post('/posts/{post}/edit', [PostController::class, 'update'])->name('post.update');
    Route::delete('/posts/{post}/delete', [PostController::class, 'destroy'])->name('post.delete');

    Route::get('post-images/{post}', [PostController::class, 'images']); // get images from edit
    Route::get('post-comments/{post}', [PostController::class, 'comments']);
    Route::delete('comments/{comment}', [PostController::class, 'deleteComment']);

    //Contact Detail
    Route::get('/contact-details', [ContactDetailController::class, 'index'])->name('contactDetail');
    Route::get('/contact-details/datatable/ssd', [ContactDetailController::class, 'serverSide']);
    Route::get('/contact-details/create', [ContactDetailController::class, 'create'])->name('contactDetail.create');
    Route::post('/contact-details', [ContactDetailController::class, 'store'])->name('contactDetail.store');
    Route::get('/contact-details/edit/{id}', [ContactDetailController::class, 'edit'])->name('contactDetail.edit');
    Route::post('/contact-details/edit/{id}', [ContactDetailController::class, 'update'])->name('contactDetail.update');

    //note
    Route::get('notes',[NoteController::class,'index'])->name('note');
    Route::get('notes/datatable/ssd', [NoteController::class, 'serverSide']);
    Route::get('notes/create', [NoteController::class, 'create'])->name('note.create');
    Route::post('notes/create', [NoteController::class, 'store'])->name('note.store');
    Route::get('notes/edit/{note}', [NoteController::class, 'edit'])->name('note.edit');
    Route::put('notes/update/{note}', [NoteController::class, 'update'])->name('note.update');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('note.destroy');
});

Route::get('image/{filename}', [ApplicationController::class, 'image'])->where('filename', '.*');
