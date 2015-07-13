<?php

namespace App\Http\Controllers;

use App\Contracts\Store;
use App\Importer\Customer\ErrorCustomer;
use App\Importer\Customer\ImportedCustomer;
use App\Importer\Customer\OsCostumer;
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

        //ImportedProduct::truncate();
        JavaScript::put([
            'url' => '/customers',
            'os_total' => OsCostumer::count(),
            'imported_total' => ImportedCustomer::count(),
            'resource' => array_values(
                array_diff(OsCostumer::lists('customers_id')->toArray(),
                    ImportedCustomer::lists('os_id')->toArray(),
                    ErrorCustomer::lists('os_id')->toArray()
                )
            )
        ]);

        return view('importer.customers');
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
                    'os_id' => $customer->id,
                    'name' => $result->customer->title,
                    'wc_id' => $result->customer->id
                ]);

                return ['success' => 1, 'message' => "Customer '{$imported['name']}' imported successfully"];
            }
        } catch (Exception $e) {
            ErrorCustomer::create([
                'os_id' => $customer->id,
                'name' => $customer->description->customers_name,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => 0,
                'message' => $e->getMessage()
            ];
        }

        return $result;
    }
}
