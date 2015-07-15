<?php

namespace App\Importer\Order;

use App\Contracts\ToWooCommerce;
use App\Importer\Customer\ImportedCustomer;
use App\Importer\Product\ImportedProduct;
use Exception;
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
 * @property mixed payment_method
 */
class OsOrder extends Model implements ToWooCommerce {

    protected $connection = 'oscommerce';
    protected $table = 'orders';
    protected $primaryKey = 'orders_id';

    public function status() {
        return $this->hasOne(OsOrderStatus::class, 'orders_status_id', 'orders_status');
    }

    public function products() {
        return $this->hasMany(OsOrderProduct::class, 'orders_id', 'orders_id');
    }

    public function total() {
        return $this->hasMany(OsOrderTotal::class, 'orders_id', 'orders_id');
    }


    public function toWooCommerce() {
        return [
            'order_number'     => $this->orders_id,
            'status'           => $this->getOrderStatus(),
            'currency'         => $this->currency,
            'payment_details'  => [
                'method_id'      => $this->getPaymentMethodId(),
                'method_title'   => $this->getPaymentMethodTitle(),
                'paid'           => true, // or false
                'transaction_id' => 'no idea'
            ],
            'billing_address'  => [
                'first_name' => $this->getFirstName($this->billing_name),
                'last_name'  => $this->getLastName($this->billing_name),
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
                'first_name' => $this->getFirstName($this->delivery_name),
                'last_name'  => $this->getLastName($this->delivery_name),
                'company'    => $this->delivery_company,
                'address_1'  => $this->delivery_street_address,
                'address_2'  => $this->delivery_suburb,
                'city'       => $this->delivery_city,
                'state'      => $this->delivery_state,
                'postcode'   => $this->delivery_postcode,
                'country'    => $this->delivery_country,
            ],
            'note'             => '',
            'customer_id'      => $this->getWooCustomerId(),
            'line_items'       => $this->getLineItems(),
            'shipping_lines'   => $this->getShippingLines(),
            'fee_lines'        => [],
            'coupon_lines'     => $this->getCouponLines(),
            'customer'         => []

        ];
    }

    private function getOrderStatus() {
        $status = $this->status->orders_status_id;
        $statusList = [
            1 => 'pending', //Pending
            2 => 'processing',//Processing
            3 => 'completed',//Delivered
            4 => 'completed',//"Intransit (Tracking Number)"
            5 => 'cancelled',//"Order Cancelled"
            6 => 'failed',//"Customer Black List"
            7 => 'processing',//"Preparing [PayPal Standard]"
            8 => 'refund',// refund
        ];

        return isset($statusList[(int) $status]) ? $statusList[(int) $status] : 'on-hold';
    }

    private function getLineItems() {
        $items = [];
        $allProducts = $this->products()->with('attributes')->get()->toArray();
        foreach ($allProducts as $product) {
            $items[] = [
                'total'      => $product['products_price'],
                'total_tax'  => ($product['products_price'] * ($product['products_tax'] / 100)),
                'quantity'   => $product['products_quantity'],
                'product_id' => $this->getWooProductId($product['products_id']),
                'variations' => empty($product['attributes']) ? [] : [
                    "pa_".strtolower($product['attributes']['products_options']) => $product['attributes']['products_options_values']
                ]
            ];
        }

        return $items;
    }

    private function getFirstName($name) {
        return current(explode(' ', $name));
    }

    private function getLastName($name) {
        return last(explode(' ', $name));
    }

    private function getShippingLines() {
        $items = [];
        $totals = $this->total()->get()->toArray();
        foreach ($totals as $tot) {
            if ($tot['class'] === 'ot_shipping') {
                $method = preg_replace('/\s\(.*|:/', '', $tot['title']);
                $items[] = [
                    'method_id'    => str_slug($method, '_'),
                    'method_title' => $method,
                    'total'        => $tot['value']
                ];
            }
        }

        return $items;
    }

    private function getCouponLines() {
        $items = [];
        $totals = $this->total()->get()->toArray();
        foreach ($totals as $tot) {
            if ($tot['class'] === 'ot_redemptions') {
                $items[] = [
                    'code'   => 'Points Redeemed',
                    'amount' => $tot['value']
                ];
            }
        }

        return $items;
    }

    private function getWooCustomerId() {
        $customer = ImportedCustomer::where('os_id', '=', $this->customers_id)->get()->first();
        if (isset($customer['wc_id'])) {
            return $customer['wc_id'];
        }

        throw new Exception("the customer has not been imported yet.");
    }

    private function getWooProductId($id) {
        $customer = ImportedProduct::where('os_id', '=', $id)->get()->first();
        if (isset($customer['wc_id'])) {
            return $customer['wc_id'];
        }

        throw new Exception("the product has not been imported yet.");
    }

    private function getPaymentMethodId() {
        $paymentMethod = $this->payment_method;
        $ids = [
            "Credit Card+Points"                                             => str_slug("Credit Card"),
            "PayPal Express (including Credit Cards and Debit Cards)"        => str_slug("PayPal Express"),
            "PayPal"                                                         => "paypal",
            "PayPal+Points"                                                  => "paypal",
            "PayPal Express (including Credit Cards and Debit Cards)+Points" => str_slug("PayPal Express"),
            "Credit Card"                                                    => str_slug("Credit Card"),
            "Bank Transfer or Deposit"                                       => "bacs",
            "Check/Money Order"                                              => "cheque",
            "Bank Transfer or Deposit+Points"                                => "bacs"
        ];

        return isset($ids[$paymentMethod]) ? $ids[$paymentMethod] : 'default';

    }

    private function getPaymentMethodTitle() {
        $paymentMethod = $this->payment_method;
        $ids = [
            "Credit Card+Points"                                             => "Credit Card",
            "PayPal Express (including Credit Cards and Debit Cards)"        => "PayPal Express",
            "PayPal"                                                         => "PayPal",
            "PayPal+Points"                                                  => "PayPal",
            "PayPal Express (including Credit Cards and Debit Cards)+Points" => "PayPal Express",
            "Credit Card"                                                    => "Credit Card",
            "Bank Transfer or Deposit"                                       => "Direct Bank Transfer",
            "Check/Money Order"                                              => "Cheque Payment",
            "Bank Transfer or Deposit+Points"                                => "Direct Bank Transfer"
        ];

        return isset($ids[$paymentMethod]) ? $ids[$paymentMethod] : 'Default';
    }
}
