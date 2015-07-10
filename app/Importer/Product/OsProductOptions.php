<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsProductOptions extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'products_options';
    protected $primaryKey = 'products_options_id';
}
