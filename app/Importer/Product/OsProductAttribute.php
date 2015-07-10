<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsProductAttribute extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'products_attributes';
    protected $primaryKey = 'products_attributes_id';

    public function product()
    {
        return $this->belongsTo(OsProduct::class, 'product_id');
    }

    public function value()
    {
        return $this->hasOne(OsProductOptionsValues::class,'products_options_values_id','options_values_id');
    }

    public function name()
    {
        return $this->hasOne(OsProductOptions::class,'products_options_id','options_id');
    }

}
