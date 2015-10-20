<?php

namespace App\Http\Controllers;

use App\Contracts\Store;
use App\Importer\Customer\ErrorCustomer;
use App\Importer\Customer\ImportedCustomer;
use App\Importer\Customer\UpdatedCustomer;
use App\Importer\Customer\OsCustomer;
use Exception;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Support\Facades\Input;
use JavaScript;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

	    // ImportedCustomer::truncate();
	    // ErrorCustomer::truncate();
        JavaScript::put([
            'url' => '/customers',
            'os_total' => OsCustomer::count(),
            'imported_total' => ImportedCustomer::count(),
            'resource' => array_values(
                array_diff(OsCustomer::orderBy('customers_id', 'desc')->lists('customers_id')->toArray(),
                    ImportedCustomer::orderBy('os_id', 'desc')->lists('os_id')->toArray(),
                    ErrorCustomer::orderBy('os_id', 'desc')->lists('os_id')->toArray()
                )
            )
        ]);

        return view('importer.index',['resource' => 'Customers']);
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
            $customer = OsCustomer::findOrFail(Input::get('resource_id'));

            $result = $store->createCustomer($customer->toWooCommerce());
            if (isset($result->customer)) {
                $imported = ImportedCustomer::create([
                    'os_id' => $customer->customers_id,
                    'email' => $result->customer->email,
                    'wc_id' => $result->customer->id
                ]);

                return ['success' => 1, 'message' => "Customer '{$imported['email']}' imported successfully"];
            }
        } catch (Exception $e) {
            ErrorCustomer::create([
                'os_id' => $customer->customers_id,
                'email' => $customer->customers_email_address,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => 0,
                'message' => $e->getMessage()
            ];
        }

        return $result;
    }


    /**
     * import customer points 
     * @return view
     */
    public function points() {
      // UpdatedOrder::truncate();
      // ErrorOrder::truncate();  

        JavaScript::put([
            'url'            => '/orders/update',
            'os_total'       => ImportedCustomer::count(),
            'imported_total' => UpdatedCustomer::count(),
            'resource'       => array_values(
                array_diff(ImportedCustomer::lists('os_id')->toArray(),
                    UpdatedCustomer::lists('os_id')->toArray()
                )
            )
        ]);

        return view('importer.index',['resource' => 'Customer Updates']);
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
        return view('importer.customers.success',['data' => ImportedCustomer::all()]);
    }

    public function errors(){
        return view('importer.customers.errors',['data' => ErrorCustomer::all()]);
    }
}
