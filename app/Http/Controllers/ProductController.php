<?php

namespace App\Http\Controllers;

use App\Contracts\Store;
use App\Importer\Product\ErrorProduct;
use App\Importer\Product\ImportedProduct;
use App\Importer\Product\OsProduct;
use Exception;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Support\Facades\Input;
use JavaScript;

class ProductController extends Controller
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
            'url' => '/products',
            'os_total' => OsProduct::count(),
            'imported_total' => ImportedProduct::count(),
            'resource' => array_values(
                array_diff(OsProduct::lists('products_id')->toArray(),
                    ImportedProduct::lists('os_id')->toArray(),
                    ErrorProduct::lists('os_id')->toArray()
                )
            )
        ]);

        return view('importer.products');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Store $store
     * @return Response
     */
    public function import()//Store $store
    {
        return ['success' => rand(0,1), 'message' => Factory::create()->text(100)];

        try {
            $product = OsProduct::findOrFail(Input::get('product_id'));

            $result = $store->createProduct($product->toWooCommerce());
            if (isset($result->product)) {
                $imported = ImportedProduct::create([
                    'os_id' => $product->products_id,
                    'name' => $product->description->title,
                    'wc_id' => $result->product->id
                ]);

                return ['success' => 1, 'message' => "Product '{$product->description->title}',  os_id:'{$product->products_id}' imported successfully"];
            }
        } catch (Exception $e) {
            ErrorProduct::create([
                'os_id' => $product->products_id,
                'name' => $product->description->products_name,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => 0,
                'message' => "Product '{$product->description->title}',  os_id:'{$product->products_id}', Error:". $e->getMessage()
            ];
        }

        return $result;
    }

}
