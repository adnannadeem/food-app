<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('reg', 'Api\v1\Auth\RegisterController@action');
    Route::post('login', 'Api\v1\Auth\LoginController@action');
    Route::get('me', 'Api\v1\Auth\MeController@action');
    Route::get('refresh', 'Api\v1\Auth\MeController@refresh');
    Route::put('profile', 'Api\v1\Auth\MeController@update');
    Route::put('device', 'Api\v1\Auth\MeController@device');
    Route::put('change-password', 'Api\v1\Auth\MeController@changePassword');
    Route::post('logout', 'Api\v1\Auth\LogoutController@action');

    Route::get('locations', 'Api\v1\LocationController@index');
    Route::get('locations/{id}/areas', 'Api\v1\LocationController@area');
    Route::get('areas', 'Api\v1\AreaController@index');
    Route::post('transactions', 'Api\v1\TransactionController@index');

    Route::get('orders', 'Api\v1\OrderController@index');
    Route::post('orders', 'Api\v1\OrderController@store');
    Route::post('orders/payment', 'Api\v1\OrderController@orderPayment');
    Route::put('orders/{id}', 'Api\v1\OrderController@update');
    Route::get('orders/{id}/show', 'Api\v1\OrderController@show');
    Route::get('orders/{id}/download-attachmement', 'Api\v1\OrderController@attachment');

    Route::get('shops/{shop}/products', 'Api\v1\ShopProductController@action');
    Route::get('shops/{shop}/categories', 'Api\v1\ShopCategoryController@action');
    Route::get('shops/{shop}/categories/{category}', 'Api\v1\ShopCategoryProductController@action');
    Route::get('shops/{shop}/products/{product}', 'Api\v1\ShopCategoryProductController@show');

    Route::get('shop-product/{shop_id}/shop/product', 'Api\v1\ShopProductController@product');
    Route::get('shop-product/{shop_id}/shop/{id}/product', 'Api\v1\ShopProductController@show');
    Route::post('shop-product/{shop_id}/shop/product', 'Api\v1\ShopProductController@store');
    Route::put('shop-product/{shop_id}/shop/{id}/product', 'Api\v1\ShopProductController@update');
    Route::delete('shop-product/{shop_id}/shop/{id}/product', 'Api\v1\ShopProductController@delete');

    Route::get('search/{shop}/shops', 'Api\v1\SearchController@shops');
    Route::get('search/{shop}/shops/{product}/products', 'Api\v1\SearchController@shopProducts');

    Route::get('status/{name}/{flip?}', 'Api\v1\StatusController@index');
    Route::get('settings', 'Api\v1\SettingController@index');

    Route::post('checkout/invoice', 'Api\v1\CheckoutController@invoice');

    Route::post('checkout/payment', 'Api\v1\CheckoutController@payment');
    Route::post('checkout/cancel', 'Api\v1\CheckoutController@cancel');

    Route::post('shop', 'Api\v1\ShopController@store');
    Route::put('shop/{id}', 'Api\v1\ShopController@update');
    Route::get('shop/{id}/show', 'Api\v1\ShopController@show');

    Route::get('products', 'Api\v1\ProductController@index');

    Route::get('customer-search', 'Api\v1\ShopOrderController@search');
    Route::get('shoporder', 'Api\v1\ShopOrderController@index');
    Route::get('shoporder/{id}', 'Api\v1\ShopOrderController@show');

    Route::post('shoporder', 'Api\v1\ShopOrderController@store');
    Route::put('shoporder/{id}', 'Api\v1\ShopOrderController@update');

    Route::get('notification-order', 'Api\v1\NotificationOrderController@index');
    Route::put('notification-order/{id}/update', 'Api\v1\NotificationOrderController@orderAccept');
    Route::put('notification-order-product-receive/{id}/update', 'Api\v1\NotificationOrderController@OrderProductReceive');
    Route::put('notification-order-status/{id}/update', 'Api\v1\NotificationOrderController@orderStatus');
    Route::get('notification-order/{id}/show', 'Api\v1\NotificationOrderController@show');
    Route::get('notification-order/history', 'Api\v1\NotificationOrderController@history');

    Route::post('shop-owner-sales-report', 'Api\v1\ShopOwnerSalesReportController@index')->name('shop-owner-sales-report.index');


    Route::get('request-product', 'Api\v1\RequestProductController@index');
    Route::post('request-product', 'Api\v1\RequestProductController@store');
    Route::put('request-product/{id}', 'Api\v1\RequestProductController@update');
    Route::get('request-product/{id}', 'Api\v1\RequestProductController@show');
    Route::delete('request-product/{id}', 'Api\v1\RequestProductController@delete');


    Route::get('product-category', 'Api\v1\ProductCategoryController@index');
    
    Route::post('get-otp', 'Api\v1\OtpController@get_otp');
    Route::post('verify-otp', 'Api\v1\OtpController@verify_otp');
});
