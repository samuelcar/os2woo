<?php

namespace App\Http\Controllers;

use App\Importer\Product\ImportedProduct;
use App\Importer\Product\OsProduct;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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

        JavaScript::put([
            'os_total' => OsProduct::count(),
            'imported_total' => ImportedProduct::count(),
            'products' => array_diff(OsProduct::lists('products_id')->toArray(),
                ImportedProduct::lists('os_id')->toArray())
        ]);

        return view('importer.products');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function import()
    {
        $product = OsProduct::find(11);

        return $product->transformToWoo();
    }

}
