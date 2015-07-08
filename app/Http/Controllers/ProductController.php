<?php

namespace App\Http\Controllers;

use App\Contracts\Store;
use App\Importer\Product\ImportedProduct;
use App\Importer\Product\OsProduct;
use Exception;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
            'os_total' => OsProduct::count(),
            'imported_total' => ImportedProduct::count(),
            'products' => array_values(array_diff(OsProduct::lists('products_id')->toArray(),
	            ImportedProduct::lists('os_id')->toArray()))
        ]);

        return view('importer.products');
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Store $store
	 *
	 * @return Response
	 */
    public function import(Store $store)
    {
	    try {
	        $product = OsProduct::findOrFail( Input::get( 'product_id' ) );

            $result  = $store->createProduct( $product->toWooCommerce() );
			if(isset($result->product)){
				$imported = ImportedProduct::create([
					'os_id' => Input::get( 'product_id' ),
					'name'  => $result->product->title,
					'wc_id' => $result->product->id
				]);

				return ['success' => 1 , 'message' => "Product '{$imported['name']}' imported successfully"];
			}
        }catch ( Exception $e){
			return ['success' => 0,
					'message' => $e->getMessage()
			];
        }
        return $result;
    }

}
