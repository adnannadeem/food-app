<?php

use App\Enums\PaymentMethod;
use App\Http\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['installed']], function () {
    Auth::routes(['register' => false, 'verify' => false]);
});

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'middleware' => ['web', 'install']], function () {
    Route::get('purchase-code', [
        'as'   => 'purchase_code',
        'uses' => 'PurchaseCodeController@index',
    ]);

    Route::post('purchase-code', [
        'as'   => 'purchase_code.check',
        'uses' => 'PurchaseCodeController@action',
    ]);
});

Route::redirect('/', 'admin/dashboard');
Route::redirect('/admin', 'admin/dashboard');
Route::redirect('/home', 'admin/dashboard');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'installed', 'admin'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {

    Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');
    Route::post('daywise-income-order', 'DashboardController@daywiseIncomeOrder')->name('dashboard.daywise-income-order');

    Route::get('profile', 'ProfileController@index')->name('profile');
    Route::put('profile/update/{profile}', 'ProfileController@update')->name('profile.update');
    Route::put('profile/change', 'ProfileController@change')->name('profile.change');

    Route::get('setting/all', 'SettingController@all')->name('setting.all');
    Route::post('setting/all', 'SettingController@update')->name('setting.update');
    Route::post('setting/all', 'SmsSettingController@update')->name('setting.sms.update');
    Route::post('setting/all', 'PaymentSettingController@update')->name('setting.payment.update');
    Route::post('setting/all', 'EmailSettingController@update')->name('setting.email.update');
    Route::post('setting/all', 'NotificationSettingController@update')->name('setting.notification.update');
    Route::post('setting/all', 'SocialLoginSettingController@update')->name('setting.social.update');
    Route::post('setting/all', 'OtpSettingController@update')->name('setting.otp.update');

    Route::get('setting', 'SettingController@index')->name('setting.index');
    Route::post('setting', 'SettingController@update')->name('setting.update');

    Route::get('setting/email', 'EmailSettingController@index')->name('setting.email');
    Route::post('setting/email', 'EmailSettingController@update')->name('setting.email.update');

    Route::get('setting/notification', 'NotificationSettingController@index')->name('setting.notification');
    Route::post('setting/notification', 'NotificationSettingController@update')->name('setting.notification.update');

    Route::get('setting/sms', 'SmsSettingController@index')->name('setting.sms');
    Route::post('setting/sms', 'SmsSettingController@update')->name('setting.sms.update');

    Route::get('setting/payment', 'PaymentSettingController@index')->name('setting.payment');
    Route::post('setting/payment', 'PaymentSettingController@update')->name('setting.payment.update');

    Route::get('setting/social', 'SocialLoginSettingController@index')->name('setting.social');
    Route::post('setting/social', 'SocialLoginSettingController@update')->name('setting.social.update');

    Route::resource('location', 'LocationController');
    Route::get('get-location', 'LocationController@getLocation')->name('location.get-location');

    Route::resource('area', 'AreaController');
    Route::get('get-area', 'AreaController@getArea')->name('area.get-area');

    Route::resource('category', 'CategoryController');
    Route::get('get-category', 'CategoryController@getCategory')->name('category.get-category');

    Route::resource('adminusers', 'AdminUserController');
    Route::get('get-adminusers', 'AdminUserController@getAdminUsers')->name('adminusers.get-adminusers');

    Route::resource('customers', 'CustomerController');
    Route::get('get-customers', 'CustomerController@getCustomers')->name('customers.get-customers');

    Route::resource('deliveryboys', 'DeliveryBoyController');
    Route::get('get-deliveryboys', 'DeliveryBoyController@getDeliveryBoy')->name('deliveryboys.get-deliveryboys');
    Route::get('get-order-history', 'DeliveryBoyController@history')->name('deliveryboys.get-order-history');

    Route::resource('shop', 'ShopController');

    Route::get('shop/{shop}/products', 'ShopController@products')->name('shop.products');
    Route::get('shop/{shop}/products/create', 'ShopController@productAdd')->name('shop.products.create');
    Route::post('shop/{shop}/products/create', 'ShopController@productStore')->name('shop.products.store');
    Route::get('shop/{shop}/products/{shopproduct}/edit', 'ShopController@shopProductEdit')->name('shop.shopproduct.edit');
    Route::put('shop/{shop}/products/{shopproduct}/update', 'ShopController@shopProductUpdate')->name('shop.products.update');
    Route::delete('shop/{shop}/products/{shopproduct}/delete', 'ShopController@shopProductDelete')->name('shop.shopproduct.delete');

    // Route::post('shop/{shop}/products/attach', 'ShopController@productAttach')->name('shop.product.attach');

    Route::get('get-shop', 'ShopController@getShop')->name('shop.get-shop');
    Route::post('get-shop', 'ShopController@getArea')->name('shop.get-area');

    Route::resource('products', 'ProductController');
    // Store ads Image
    Route::post('getMedia', 'ProductController@getMedia')->name('products.getMedia');
    Route::post('storeMedia', 'ProductController@storeMedia')->name('products.storeMedia');
    Route::post('storeMedia/{product}', 'ProductController@updateMedia')->name('products.updateMedia');
    Route::post('removeMedia', 'ProductController@removeMedia')->name('products.removeMedia');
    Route::post('deleteMedia', 'ProductController@deleteMedia')->name('products.deleteMedia');
    Route::get('get-products', 'ProductController@getProduct')->name('products.get-product');

    Route::resource('orders', 'OrderController');
    Route::get('orders/{order}/delivery', 'OrderController@delivery')->name('orders.delivery');
    Route::get('get-orders', 'OrderController@getOrder')->name('orders.get-orders');

    Route::resource('updates', 'UpdateController');
    Route::get('get-updates', 'UpdateController@getUpdates')->name('updates.get-updates');
    Route::get('checking-updates', 'UpdateController@checking')->name('updates.checking-updates');
    Route::get('update', 'UpdateController@update')->name('updates.update');
    Route::get('update-log', 'UpdateController@log')->name('updates.update-log');

    Route::get('payment', 'PaymentController@index')->name('payment.index');
    Route::get('payment/invoice', 'PaymentController@invoice')->name('payment.invoice');
    Route::get('payment/cancel', 'PaymentController@cancel')->name('payment.cancel');

    Route::get('transaction', 'TransactionController@index')->name('transaction.index');
    Route::get('get-transaction', 'TransactionController@getTransaction')->name('transaction.get-transaction');

    Route::get('shop-owner-sales-report', 'ShopOwnerSalesReportController@index')->name('shop-owner-sales-report.index');
    Route::post('shop-owner-sales-report', 'ShopOwnerSalesReportController@index')->name('shop-owner-sales-report.index');

    Route::get('admin-commission-report', 'AdminCommissionReportController@index')->name('admin-commission-report.index');
    Route::post('admin-commission-report', 'AdminCommissionReportController@index')->name('admin-commission-report.index');

});

Route::get('webview/paypal/{id}', 'Admin\WebviewController@paypal')->name('webview.paypal');
Route::post('webview/paypal/payment', 'Admin\WebviewController@paypalpayment')->name('webview.paypal.payment');
Route::get('webview/paypal/{id}/return', 'Admin\WebviewController@paypalReturn')->name('webview.paypal.return');
Route::get('webview/paypal/{id}/cancel', 'Admin\WebviewController@paypalCancel')->name('webview.paypal.cancel');

Route::get('webview/stripe', 'Admin\WebviewController@stripe')->name('webview.stripe');
Route::get('webview/stripe', 'Admin\WebviewController@stripe')->name('webview.stripe');

Route::get('paypal/ec-checkout', 'Admin\PayPalController@getExpressCheckout');
Route::get('paypal/ec-checkout-success', 'Admin\PayPalController@getExpressCheckoutSuccess');
Route::get('paypal/adaptive-pay', 'Admin\PayPalController@getAdaptivePay');
Route::post('paypal/notify', 'Admin\PayPalController@notify');


Route::get('lolpayment/{id}', function($id) {
    $checkout = new CheckoutService($id, PaymentMethod::CASH_ON_DELIVERY);
    return $checkout->payment();
});
