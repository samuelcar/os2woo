<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsProductOptionsValues extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'products_options_values';
    protected $primaryKey = 'products_options_values_id';

    public function attribute()
    {
        return $this->belongsTo(OsProductAttribute::class,'products_options_values_id','products_options_values_id');
    }
}
