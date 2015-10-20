<?php
namespace App\Http\Controllers;

use App\Contracts\Store;
use App\Importer\Product\ErrorProduct;
use App\Importer\Product\ImportedProduct;
use App\Importer\Product\OsProduct;
use App\Importer\Product\Category;
use Exception;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Faker\Factory;
use Illuminate\Support\Facades\Input;
use JavaScript;
use Log;

class ProductController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @param Store $store
     *
     * @return Response
     */
    public function index(Store $store) {
        
       // ImportedProduct::truncate();
       // ErrorProduct::truncate();
        JavaScript::put(['url' => '/products', 'os_total' => OsProduct::count(), 'imported_total' => ImportedProduct::count(), 'resource' => array_values(array_diff(OsProduct::orderBy('products_id', 'desc')->lists('products_id')->toArray(), ImportedProduct::orderBy('os_id', 'desc')->lists('os_id')->toArray(), ErrorProduct::orderBy('os_id', 'desc')->lists('os_id')->toArray())) ]);
        
        return view('importer.index', ['resource' => 'Products']);
    }
    
    /**
     * Show the form for creating a new resource_idurce.
     *
     * @param Store $store
     * @return Response
     */
    public function import(Store $store) {
        
        //        return ['success' => rand(0,1), 'message' => Factory::create()->text(100)];
        $product = OsProduct::findOrFail(Input::get('resource_id'));
        try {
            
            $result = $store->createProduct($product->toWooCommerce());
            
            // $result = $store->updateProduct($result->product->id, $product->toWooCommerce());
            if (isset($result->product)) {
                ImportedProduct::create(['os_id' => $product->products_id, 'name' => $result->product->title, 'wc_id' => $result->product->id]);
                
                return ['success' => 1, 'message' => "Product '{$result->product->title}',  os_id:'{$product->products_id}' imported successfully"];
            }
        }
        catch(Exception $e) {
            ErrorProduct::create(['os_id' => $product->products_id, 'name' => $product->description->products_name, 'error' => $e->getMessage() ]);
            
            return ['success' => 0, 'message' => "Product '{$product->description->products_name}',  os_id:'{$product->products_id}'," . $e->getMessage() ];
        }
        
        return $result;
    }
    
    public function imported() {
        return view('importer.products.success', ['data' => ImportedProduct::all() ]);
    }
    
    public function errors() {
        return view('importer.products.errors', ['data' => ErrorProduct::all() ]);
    }
}
