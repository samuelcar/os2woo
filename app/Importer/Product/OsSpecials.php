<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsSpecials extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'specials';
    protected $primaryKey = 'specials_id';

    public function product()
    {
        return $this->belongsTo(OsProduct::class,'products_id','products_id');
    }
}