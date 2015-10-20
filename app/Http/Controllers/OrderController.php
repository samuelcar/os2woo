<?php

namespace App\Http\Controllers;

use App\Contracts\Store;
use App\Importer\Customer\ImportedCustomer;
use App\Importer\Order\ErrorOrder;
use App\Importer\Order\ImportedOrder;
use App\Importer\Order\OsOrder;
use App\Importer\Order\UpdatedOrder;
use App\Importer\WooPost;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Support\Facades\Input;
use JavaScript;

class OrderController extends Controller {

    public function index() {

       // ImportedOrder::truncate();
       // ErrorOrder::truncate();

        JavaScript::put([
            'url'            => '/orders',
            'os_total'       => OsOrder::count(),
            'imported_total' => ImportedOrder::count(),
            'resource'       => array_values(
                array_diff(OsOrder::orderBy('orders_id', 'desc')->lists('orders_id')->toArray(),
                    ImportedOrder::orderBy('os_id', 'desc')->lists('os_id')->toArray(),
                    ErrorOrder::orderBy('os_id', 'desc')->lists('os_id')->toArray()
                )
            )
        ]);

        return view('importer.index',['resource' => 'Orders']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Store $store
     *
     * @return Response
     */
    public function import(Store $store) {
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
                'message' => 'Order id:'.$order->orders_id." ".$e->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Function to setup the view to update the orders
     *
     * @return \Illuminate\View\View
     */
    public function dates() {
      // UpdatedOrder::truncate();
      // ErrorOrder::truncate();  

        JavaScript::put([
            'url'            => '/orders/update',
            'os_total'       => ImportedOrder::count(),
            'imported_total' => UpdatedOrder::count(),
            'resource'       => array_values(
                array_diff(ImportedOrder::lists('os_id')->toArray(),
                    UpdatedOrder::lists('os_id')->toArray()
                )
            )
        ]);

        return view('importer.index',['resource' => 'Order Updates']);
    }

    /**
     * Handle the update of orders
     *
     * @return array
     */
    public function update() {
        try {
            $osOrder = OsOrder::findOrFail(Input::get('resource_id'));
            $importedOrder = ImportedOrder::where('os_id', '=', Input::get('resource_id'))->first();
            $woOrder = WooPost::findOrFail($importedOrder->wc_id);

            if ($woOrder->post_type == 'shop_order') {
                $purchasedDate = $osOrder->date_purchased;
                $modifiedDate = $osOrder->last_modified;
                $woOrder->post_date = $woOrder->post_date_gmt = $purchasedDate;
                $woOrder->post_modified = $woOrder->post_modified_gmt = $modifiedDate;
                $woOrder->post_title = "Order &ndash; ".date('M d, Y @ h:i A', strtotime($purchasedDate));
                $woOrder->save();
                UpdatedOrder::create([
                    'os_id'  => $importedOrder->os_id,
                    'update' => $purchasedDate,
                    'wc_id'  => $importedOrder->wc_id,
                ]);

                return [
                    'success' => 1,
                    'message' => "Order '{$importedOrder->os_id}' updated on Woo Order '{$importedOrder->wc_id}' with date $purchasedDate successfully"
                ];
            }

        } catch (Exception $e) {
            ErrorOrder::create([
                'os_id' => Input::get('resource_id'),
                'email' => 'no mail',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => 0,
                'message' => 'Order id:'.Input::get('resource_id')." ".$e->getMessage()
            ];
        }

        return 1;
    }

    public function imported(){
        return view('importer.orders.success',['data' => ImportedOrder::all()]);
    }

    public function errors(){
        return view('importer.orders.errors',['data' => ErrorOrder::all()]);
    }
}
