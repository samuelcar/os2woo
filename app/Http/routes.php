<?php
use App\Importer\Customer\OsCustomer;

get('/', function () {
    return view('home');
});

get('/changemail', function(){
	$customers = OsCustomer::all();
	$faker = new \Faker\Factory();

	foreach($customers as $customer ){
		$customer->customers_email_address = $faker->create()->email;
		$customer->save();
		echo $customer->customers_email_address.'<br>';
	}
});
post('products', 'ProductController@import');
post('customers', 'CustomerController@import');
post('orders', 'OrderController@import');

get('products', 'ProductController@index');
get('customers', 'CustomerController@index');
get('orders', 'OrderController@index');
get('coupons', 'CouponController@index');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
