<?php

namespace App\Importer\Customer;

use Illuminate\Database\Eloquent\Model;

class OsAddressBook extends Model
{
    protected $connection = 'oscommerce';
    protected $table = 'address_book';
    protected $primaryKey = 'address_book_id';

	public function country() {
		return $this->hasOne( OsCountry::class, 'countries_id','entry_country_id');
	}
}
