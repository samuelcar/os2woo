<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

class OsCategory extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'categories';
    protected $primaryKey = 'categories_id';

    public function description() {
        return $this->hasOne(OsCategoryDescription::class,'categories_id','categories_id');
    }
}
