<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsCategoryDescription extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'categories_description';
    protected $primaryKey = 'categories_id';

    public function description() {
        return $this->belongsTo(OsCategoryDescription::class,'categories_id','categories_id');
    }
}