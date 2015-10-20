<?php
use App\Importer\Customer\OsCustomer;

get('/', function () {
    return view('home');
});

get('/changemail', function(){
	$customers = OsCustomer::where('customers_id','<',25000)->where('customers_id','>',19999)->get();
	$faker = new \Faker\Factory();

	foreach($customers as $customer ){
		$customer->customers_email_address = $faker->create()->email;
		$customer->customers_firstname = $faker->create()->firstName;
		$customer->customers_lastname = $faker->create()->lastName;
		$customer->save();
	}
	echo "done";
});

post('products', 'ProductController@import');
post('customers', 'CustomerController@import');
post('orders', 'OrderController@import');
post('orders/update', 'OrderController@update');
post('customers/update', 'CustomerController@update');

get('products', 'ProductController@index');
get('products/imported', 'ProductController@imported');
get('products/errors', 'ProductController@errors');
get('customers', 'CustomerController@index');
get('customers/imported', 'CustomerController@imported');
get('customers/errors', 'CustomerController@errors');
get('orders', 'OrderController@index');
get('orders/update', 'OrderController@dates');
get('customers/update', 'CustomerController@points');
get('orders/imported', 'OrderController@imported');
get('orders/errors', 'OrderController@errors');
get('coupons', 'CouponController@index');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
