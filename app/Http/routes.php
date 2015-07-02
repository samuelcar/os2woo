<?php
get('/',function(){
   return view('home');
});

Route::get('products','ProductController@index');
Route::get('customers','CustomerController@index');
Route::get('orders','OrderController@index');
Route::get('coupons','CouponController@index');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
