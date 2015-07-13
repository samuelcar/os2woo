<?php

namespace App\Importer\Customer;

use App\Contracts\ToWooCommerce;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed customers_email_address
 * @property mixed customers_firstname
 * @property mixed customers_lastname
 */
class OsCostumer extends Model implements ToWooCommerce
{
    protected $connection = 'oscommerce';
    protected $table = 'customers';
    protected $primaryKey = 'customers_id';

    public function addressBook()
    {
        return $this->hasMany(OsAddressBook::class,'customers_id', 'customers_id');
    }

    public function toWooCommerce()
    {
        return [
            "email" => $this->customers_email_address,
            "first_name" => $this->customers_firstname,
            "last_name" => $this->customers_lastname,
            "password" => wp_generate_password(),
            "billing_address" => $this->getBillingAddress(),
            "shipping_address" => $this->getShippingAddress(),
        ];
    }

    private function getBillingAddress() {
        $address = [];

        $addressBook = $this->addressBook()->get()->toArray();
        foreach($addressBook as $addr){

        }

        return $address;
first_name	string	First name
last_name	string	Last name
company	string	Company name
address_1	string	Address line 1
address_2	string	Address line 2
city	string	City name
state	string	ISO code or name of the state, province or district
postcode	string	Postal code
country	string	ISO code of the country
email	string	Email address
phone	string	Phone

    }




Attribute	Type	Description
first_name	string	First name
last_name	string	Last name
company	string	Company name
address_1	string	Address line 1
address_2	string	Address line 2
city	string	City name
state	string	ISO code or name of the state, province or district
postcode	string	Postal code
country	string	ISO code of the country
        ];
    }
}
