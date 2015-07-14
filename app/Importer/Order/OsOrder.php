<?php

namespace App\Importer\Order;

use App\Contracts\ToWooCommerce;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed orders_id
 * @property mixed status
 * @property mixed currency
 * @property mixed customers_id
 */
class OsOrder extends Model implements ToWooCommerce {

	protected $connection = 'oscommerce';
	protected $table = 'orders';
	protected $primaryKey = 'orders_id';

	public function status() {
		return $this->hasOne( OsOrderStatus::class, 'orders_status', 'orders_status_id' );
	}


	public function toWooCommerce() {
		return [
			'order_number' => $this->orders_id,
			'status'       => $this->getOrderStatus(),
			'currency'     => $this->currency,
            'payment_details' => [
                'method_id' => '',
                'method_title' => '',
                'paid' => true, // or false
                'transaction_id' => 'no idea'
            ],
            'billing_address'=> [

            ],
            'shipping_address' => [],
            'note' => '',
            'customer_id' => $this->customers_id,
            'line_items' => $this->getLineItems(),
            'shipping_lines' => [],
            'fee_lines' => [],
            'coupon_lines' => [],
            'customer' => []

		];

		/*		Orders Properties
		Payment Details Properties
		Line Items Properties

		Attribute	Type	Description
		id	integer	Line item ID READ-ONLY
		subtotal	float	Line item subtotal
		subtotal_tax	float	Line item tax subtotal
		total	float	Line item total
		total_tax	float	Line item tax total
		price	float	Product price READ-ONLY
		quantity	integer	Quantity
		tax_class	string	Product tax class READ-ONLY
		name	string	Product name READ-ONLY
		product_id	integer	Product ID REQUIRED
		sku	string	Product SKU READ-ONLY
		meta	array	List of product meta items. See Products Meta Items Properties
		variations	array	List of product variation attributes. e.g: "variation": {"pa_color": "Black", "pa_size": "XGG"} (Use pa_ prefix when is a product attribute) WRITE-ONLY
		Products Meta Items Properties

		Attribute	Type	Description
		key	string	Meta item key
		label	string	Meta item label
		value	string	Meta item value
		Shipping Lines Properties

		Attribute	Type	Description
		id	integer	Shipping line ID READ-ONLY
		method_id	string	Shipping method ID REQUIRED
		method_title	string	Shipping method title REQUIRED
		total	float	Total amount
		Tax Lines Properties

		Attribute	Type	Description
		id	integer	Tax rate line ID READ-ONLY
		rate_id	integer	Tax rate ID READ-ONLY
		code	string	Tax rate code READ-ONLY
		title	string	Tax rate title/name READ-ONLY
		total	float	Tax rate total READ-ONLY
		compound	boolean	Shows if is or not a compound rate. Compound tax rates are applied on top of other tax rates. READ-ONLY
		Fee Lines Properites

		Attribute	Type	Description
		id	integer	Fee line ID READ-ONLY
		title	string	Shipping method title REQUIRED
		taxable	boolean	Shows/define if the fee is taxable WRITE-ONLY
		tax_class	string	Tax class, requered in write-mode if the fee is taxable
		total	float	Total amount
		total_tax	float	Tax total
		Coupon Lines Properties

		Attribute	Type	Description
		id	integer	Coupon line ID READ-ONLY
		code	string	Coupon code REQUIRED
		amount	float	Total amount REQUIRED*/
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

    private function getPaymentDetails() { }

    private function getLineItems() { }
}
