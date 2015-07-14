<?php

namespace App\Importer\Order;

use Illuminate\Database\Eloquent\Model;

class OsOrderStatus extends Model
{
	protected $connection = 'oscommerce';
	protected $table = 'orders_status';
	protected $primaryKey = 'orders_status_id';
}
