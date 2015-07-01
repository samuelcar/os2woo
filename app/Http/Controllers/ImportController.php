<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    protected $store;

    function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function products($product_id)
    {

    }

    public function customers()
    {

    }
}
