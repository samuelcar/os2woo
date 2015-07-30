<?php

namespace App\Importer\Customer;

use App\Contracts\ToWooCommerce;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed customers_email_address
 * @property mixed customers_firstname
 * @property mixed customers_lastname
 * @property mixed customers_telephone
 * @property mixed addressBook
 * @property mixed customers_default_address_id
 */
class OsCustomer extends Model implements ToWooCommerce {

	protected $connection = 'oscommerce';
	protected $table = 'customers';
	protected $primaryKey = 'customers_id';
	public   $timestamps = false;

	public function addressBook() {
		return $this->hasMany( OsAddressBook::class, 'customers_id', 'customers_id' );
	}

	public function toWooCommerce() {
		return [
			"email"            => $this->customers_email_address,
			"first_name"       => $this->customers_firstname,
			"last_name"        => $this->customers_lastname,
			"password"         => wp_generate_password(),
			"billing_address"  => $this->getBillingAddress(),
			"shipping_address" => $this->getShippingAddress(),
		];
	}

	public function getBillingAddress() {
		$addressBook = $this->addressBook()->with( 'country' )->get()->toArray();
		if ( count( $addressBook ) == 1 ) {
			$address = current( $addressBook );

			return [
				'first_name' => $address['entry_firstname'],
				'last_name'  => $address['entry_lastname'],
				'company'    => $address['entry_company'],
				'address_1'  => $address['entry_street_address'],
				'address_2'  => $address['entry_suburb'],
				'city'       => $address['entry_city'],
				'state'      => $address['entry_state'],
				'postcode'   => $address['entry_postcode'],
				'country'    => $address['country']['countries_iso_code_2'],
				'email'      => $this->customers_email_address,
				'phone'      => $this->customers_telephone

			];
		} elseif ( count( $addressBook ) > 1 ) {
			foreach ( $addressBook as $address ) {
				if ( $this->customers_default_address_id == $address['address_book_id'] ) {
					return [
						'first_name' => $address['entry_firstname'],
						'last_name'  => $address['entry_lastname'],
						'company'    => $address['entry_company'],
						'address_1'  => $address['entry_street_address'],
						'address_2'  => $address['entry_suburb'],
						'city'       => $address['entry_city'],
						'state'      => $address['entry_state'],
						'postcode'   => $address['entry_postcode'],
						'country'    => $address['country']['countries_iso_code_2'],
						'email'      => $this->customers_email_address,
						'phone'      => $this->customers_telephone

					];
				}
			}
		}

		return [];
	}

	public function getShippingAddress() {

		$addressBook = $this->addressBook()->with( 'country' )->get()->toArray();

		foreach ( $addressBook as $address ) {
			if ( $this->customers_default_address_id != $address['address_book_id'] ) {
				return [
					'first_name' => $address['entry_firstname'],
					'last_name'  => $address['entry_lastname'],
					'company'    => $address['entry_company'],
					'address_1'  => $address['entry_street_address'],
					'address_2'  => $address['entry_suburb'],
					'city'       => $address['entry_city'],
					'state'      => $address['entry_state'],
					'postcode'   => $address['entry_postcode'],
					'country'    => $address['country']['countries_iso_code_2'],
				];

			}
		}

		return [];
	}
}
