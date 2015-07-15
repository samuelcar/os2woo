<?php

namespace App\Http\Controllers;

use App\Contracts\Store;
use App\Importer\Customer\ImportedCustomer;
use App\Importer\Order\ErrorOrder;
use App\Importer\Order\ImportedOrder;
use App\Importer\Order\OsOrder;
use Exception;
use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Support\Facades\Input;
use JavaScript;

class OrderController extends Controller
{
	public function index()
	{

		//	    ImportedOrder::truncate();
		//	    ErrorOrder::truncate();

		JavaScript::put([
			'url' => '/orders',
			'os_total' => OsOrder::count(),
			'imported_total' => ImportedOrder::count(),
			'resource' => array_values(
				array_diff(OsOrder::lists('orders_id')->toArray(),
					ImportedOrder::lists('os_id')->toArray(),
					ErrorOrder::lists('os_id')->toArray()
				)
			)
		]);

		return view('importer.orders');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Store $store
	 * @return Response
	 */
	public function import(Store $store)
	{
		//        return ['success' => rand(0,1), 'message' => Factory::create()->text(100)];
		try {
			$order = OsOrder::findOrFail(Input::get('resource_id'));

			$result = $store->createOrder($order->toWooCommerce());
			if (isset($result->order)) {
				$imported = ImportedOrder::create([
					'os_id' => $order->orders_id,
					'email' => $order->customers_email_address,
					'wc_id' => $result->order->id
				]);

				return ['success' => 1, 'message' => "Order '{$imported['os_id']}' imported successfully"];
			}
		} catch (Exception $e) {
			ErrorOrder::create([
				'os_id' => $order->orders_id,
				'email' => $order->customers_email_address,
				'error' => $e->getMessage()
			]);

			return [
				'success' => 0,
				'message' => 'Order id:'.$order->orders_id." ". $e->getMessage()
			];
		}

		return $result;
	}
}
