<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsDescription extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'products_description';
    protected $primaryKey = 'products_id';
}
