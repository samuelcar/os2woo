<?php

namespace App\Importer\Order;

use Illuminate\Database\Eloquent\Model;

class OsOrderTotal extends Model
{
	protected $connection = 'oscommerce';
	protected $table = 'orders_total';
	protected $primaryKey = 'orders_total_id';
}
