<?php

namespace App\Importer\Order;

use Illuminate\Database\Eloquent\Model;

class OsOrderProductAttribute extends Model
{
	protected $connection = 'oscommerce';
	protected $table = 'orders_products_attributes';
	protected $primaryKey = 'orders_products_attributes_id';
}
