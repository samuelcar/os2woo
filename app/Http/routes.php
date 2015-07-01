<?php
get('/',function(){
   return view('home');
});

Route::get('products','ProductController@index');




Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
