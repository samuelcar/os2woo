<?php namespace App\Importer;

use App\Contracts\Store;
use WC_API_Client;
use WC_API_Client_Products;
use WC_API_Client_Resource_Customers;
use WC_API_Client_Resource_Products;

class WooCommerceStore implements Store
{

    private $url;
    private $consumer_key;
    private $consumer_secret;
    private $client;

    function __construct($url, $consumer_key, $consumer_secret)
    {
        $this->url = $url;
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->client = new WC_API_Client($url, $consumer_key, $consumer_secret);

    }

    public function createCoupon(array $coupon)
    {
        return $this->client->post('coupons', [
            'json' => [
                'coupon' => $coupon
            ]
        ]);
    }

    public function createProduct(array $product)
    {
        $client = new WC_API_Client_Resource_Products($this->client);

        return $client->create([
                'product' => $product
            ]
        );
    }

    public function updateProduct($id, array $product)
    {
	    $client = new WC_API_Client_Resource_Products($this->client);

	    return $client->update($id, [
                'product' => $product
            ]
        );
    }

    public function createCustomer(array $customer)
    {
        $client = new WC_API_Client_Resource_Customers($this->client);

        return $client->create([
                'customer' => $customer
            ]
        );
    }

    public function createOrder(array $order)
    {
	    $client = new \WC_API_Client_Resource_Orders($this->client);

	    return $client->create([
			    'order' => $order
		    ]
	    );
    }

    public function getCoupon($id)
    {
        $result = $this->client->get("coupons/$id");

        return $result->json();
    }

    public function getCouponByCode($code)
    {
        $result = $this->client->get("coupons/code/$code");

        return $result->json();
    }

    public function getCoupons()
    {
        $result = $this->client->get("coupons");

        return $result->json();
    }

    public function getCouponsCount()
    {
        $result = $this->client->get("coupons/count");

        return $result->json();
    }

    public function getCostumer($id)
    {
        $result = $this->client->get("customers/$id");

        return $result->json();
    }

    public function getCostumerByEmail($email)
    {
        $result = $this->client->get("customers/email/$email");

        return $result->json();
    }

    public function getCostumers()
    {
        $result = $this->client->get("customers");

        return $result->json();
    }

    public function getCostumerOrders($id)
    {
        $result = $this->client->get("customers/$id/orders");

        return $result->json();
    }

    public function getCostumerCount()
    {
        $result = $this->client->get("customers/count");

        return $result->json();
    }

    public function getOrder($id)
    {
        $result = $this->client->get("orders/$id");

        return $result->json();
    }

    public function getOrders($filter = null)
    {

    }

    public function getProducts()
    {
        $result = $this->client->get(1795);

        return $result;
    }

}