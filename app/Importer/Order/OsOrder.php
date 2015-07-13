<?php

namespace App\Importer\Order;

use App\Contracts\ToWooCommerce;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed orders_id
 * @property mixed status
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

		];

		/*		Orders Properties
		Attribute	Type	Description
		status	string	Order status. By default are available the status: pending, processing, on-hold, completed, cancelled, refunded and failed. See View List of Order Statuses
		currency	string	Currency in ISO format, e.g USD
		total	float	Order total READ-ONLY
		subtotal	float	Order subtotal READ-ONLY
		total_line_items_quantity	integer	Total of order items READ-ONLY
		total_tax	float	Order tax total READ-ONLY
		total_shipping	float	Order shipping total READ-ONLY
		cart_tax	float	Order cart tax READ-ONLY
		shipping_tax	float	Order shipping tax READ-ONLY
		total_discount	float	Order total discount READ-ONLY
		shipping_methods	string	Text list of the shipping methods used in the order READ-ONLY
		payment_details	array	List of payment details. See Payment Details Properties
		billing_address	array	List of customer billing address. See Customer Billing Address Properties
		shipping_address	array	List of customer shipping address. See Customer Shipping Address Properties
		note	string	Customer order notes
		customer_ip	string	Customer IP address READ-ONLY
		customer_user_agent	string	Customer User-Agent READ-ONLY
		customer_id	integer	Customer ID (user ID) REQUIRED
		view_order_url	string	URL to view the order in frontend READ-ONLY
		line_items	array	List of order line items. See Line Items Properties
		shipping_lines	array	List of shipping line items. See Shipping Lines Properties
		tax_lines	array	List of tax line items. See Tax Lines Properties READ-ONLY
		fee_lines	array	List of fee line items. See Fee Lines Properites
		coupon_lines	array	List of cupon line items. See Coupon Lines Properties
		customer	array	Customer data. See Customer Properties
		Payment Details Properties

		Attribute	Type	Description
		method_id	string	Payment method ID REQUIRED
		method_title	string	Payment method title REQUIRED
		paid	boolean	Shows/define if the order is paid using this payment method. Use true to complate the payment.
																												 transaction_id	string	Transaction ID, an optional field to set the transacion ID when complate one payment (to set this you need set the paid as true too)
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
				return '';
		}

	}
}
