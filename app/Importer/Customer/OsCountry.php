<?php

namespace App\Importer\Customer;

use Illuminate\Database\Eloquent\Model;

class OsCountry extends Model
{
	protected $connection = 'oscommerce';
	protected $table = 'countries';
	protected $primaryKey = 'countries_id';

	public function address(){
		return $this->hasMany(OsAddressBook::class,'entry_country_id','countries_id');
	}
}
