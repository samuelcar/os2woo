<?php

namespace App\Importer\Order;

use App\Contracts\ToWooCommerce;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed orders_id
 * @property mixed status
 * @property mixed currency
 * @property mixed customers_id
 * @property mixed billing_name
 * @property mixed billing_company
 * @property mixed billing_street_address
 * @property mixed billing_suburb
 * @property mixed billing_city
 * @property mixed billing_state
 * @property mixed billing_postcode
 * @property mixed billing_country
 * @property mixed customers_email_address
 * @property mixed customers_telephone
 * @property mixed delivery_name
 * @property mixed delivery_company
 * @property mixed delivery_street_address
 * @property mixed delivery_suburb
 * @property mixed delivery_city
 * @property mixed delivery_state
 * @property mixed delivery_postcode
 * @property mixed delivery_country
 */
class OsOrder extends Model implements ToWooCommerce {

	protected $connection = 'oscommerce';
	protected $table = 'orders';
	protected $primaryKey = 'orders_id';

	public function status() {
		return $this->hasOne( OsOrderStatus::class, 'orders_status_id', 'orders_status' );
	}

	public function products() {
		return $this->hasMany( OsOrderProduct::class, 'orders_id', 'orders_id' );
	}


	public function toWooCommerce() {
		return [
			'order_number'     => $this->orders_id,
			'status'           => $this->getOrderStatus(),
			'currency'         => $this->currency,
			'payment_details'  => [
				'method_id'      => '',
				'method_title'   => '',
				'paid'           => true, // or false
				'transaction_id' => 'no idea'
			],
			'billing_address'  => [
				'first_name' => $this->getFirstName( $this->billing_name ),
				'last_name'  => $this->getLastName( $this->billing_name ),
				'company'    => $this->billing_company,
				'address_1'  => $this->billing_street_address,
				'address_2'  => $this->billing_suburb,
				'city'       => $this->billing_city,
				'state'      => $this->billing_state,
				'postcode'   => $this->billing_postcode,
				'country'    => $this->billing_country,
				'email'      => $this->customers_email_address,
				'phone'      => $this->customers_telephone,
			],
			'shipping_address' => [
				'first_name' => $this->getFirstName( $this->delivery_name ),
				'last_name'  => $this->getLastName( $this->delivery_name ),
				'company'    => $this->delivery_company,
				'address_1'  => $this->delivery_street_address,
				'address_2'  => $this->delivery_suburb,
				'city'       => $this->delivery_city,
				'state'      => $this->delivery_state,
				'postcode'   => $this->delivery_postcode,
				'country'    => $this->delivery_country,
			],
			'note'             => '',
			'customer_id'      => $this->customers_id,
			'line_items'       => $this->getLineItems(),
			'shipping_lines'   => $this->getShippingLines(),
			'fee_lines'        => [ ],
			'coupon_lines'     => [ ],
			'customer'         => [ ]

		];

		/**
		 * Shipping Lines Properties
		 * Attribute    Type    Description
		 * id    integer    Shipping line ID READ-ONLY
		 * method_id    string    Shipping method ID REQUIRED
		 * method_title    string    Shipping method title REQUIRED
		 * total    float    Total amount
		 * Tax Lines Properties
		 * Attribute    Type    Description
		 * id    integer    Tax rate line ID READ-ONLY
		 * rate_id    integer    Tax rate ID READ-ONLY
		 * code    string    Tax rate code READ-ONLY
		 * title    string    Tax rate title/name READ-ONLY
		 * total    float    Tax rate total READ-ONLY
		 * compound    boolean    Shows if is or not a compound rate. Compound tax rates are applied on top of other tax rates. READ-ONLY
		 * Fee Lines Properites
		 * Attribute    Type    Description
		 * id    integer    Fee line ID READ-ONLY
		 * title    string    Shipping method title REQUIRED
		 * taxable    boolean    Shows/define if the fee is taxable WRITE-ONLY
		 * tax_class    string    Tax class, requered in write-mode if the fee is taxable
		 * total    float    Total amount
		 * total_tax    float    Tax total
		 * Coupon Lines Properties
		 * Attribute    Type    Description
		 * id    integer    Coupon line ID READ-ONLY
		 * code    string    Coupon code REQUIRED
		 * amount    float    Total amount REQUIRED*/
	}

	private function getOrderStatus() {
		$status = $this->status->orders_status_id;

		switch ( $status ) {
			case 1:
				return 'pending';
				break;
			case 2:
				return 'processing';
				break;
			case 3:
				return 'completed';
				break;
			case 4:
				return 'on-hold';
				break;
			case 5:
				return 'cancelled';
				break;
			case 6:
				return 'failed';
				break;
			case 8:
				return 'refund';
				break;
			case 7:
			default:
				return 'on-hold';
		}

	}

	public function getLineItems() {
		$items       = [ ];
		$allProducts = $this->products()->with( 'attributes' )->get()->toArray();
		foreach ( $allProducts as $product ) {
			$items[] = [
				'total'      => $product['products_price'],
				'total_tax'  => ( $product['products_price'] * ( $product['products_tax'] / 100 ) ),
				'quantity'   => $product['products_quantity'],
				'product_id' => $product['products_id'],
				'variations' => empty( $product['attributes'] ) ? [ ] : [
					"pa_" . strtolower( $product['attributes']['products_options'] ) => $product['attributes']['products_options_values']
				]
			];
		}

		return $items;
	}

	public function getFirstName( $name ) {
		return current( explode( ' ', $name ) );
	}

	public function getLastName( $name ) {
		return last( explode( ' ', $name ) );
	}

	private function getShippingLines() {

	}
}
