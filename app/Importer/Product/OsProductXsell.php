<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsProductXsell extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'products_xsell';
    protected $primaryKey = 'ID';
}
