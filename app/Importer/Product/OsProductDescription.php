<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsProductDescription extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'products_description';
    protected $primaryKey = 'products_id';

    public function product(){
        return $this->belongsTo(OsProduct::class,'products_id','products_id');
    }
}
