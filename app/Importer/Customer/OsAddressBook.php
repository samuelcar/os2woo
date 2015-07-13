<?php

namespace App\Importer\Customer;

use Illuminate\Database\Eloquent\Model;

class OsAddressBook extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'customers';
    protected $primaryKey = 'customers_id';
}
