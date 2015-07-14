<?php

namespace App\Importer\Order;

use Illuminate\Database\Eloquent\Model;

class OsOrderProduct extends Model
{
	protected $connection = 'oscommerce';
	protected $table = 'orders_products';
	protected $primaryKey = 'orders_products_id';

	public function attributes(){
		return $this->hasOne(OsOrderProductAttribute::class,'orders_products_id','orders_products_id');
	}
}
