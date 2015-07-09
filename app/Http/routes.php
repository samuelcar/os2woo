<?php
get('/', function () {
    return view('home');
});
post('products', 'ProductController@import');
post('customers', 'CustomerController@import');

get('products', 'ProductController@index');
get('customers', 'CustomerController@index');
get('orders', 'OrderController@index');
get('coupons', 'CouponController@index');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
