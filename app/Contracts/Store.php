<?php
namespace App\Contracts;

interface Store
{

    public function createCoupon(array $coupon);

    public function createProduct(array $product);

    public function createCustomer(array $customer);

    public function createOrder(array $order);

    public function getCoupon($id);

    public function getCouponByCode($code);

    public function getCoupons();

    public function getCouponsCount();

    public function getCostumer($id);

    public function getCostumerByEmail($email);

    public function getCostumers();

    public function getCostumerOrders($id);

    public function getCostumerCount();

    public function getOrder($id);

    public function getOrders($filter = null);

    public function getProducts();

	public function updateProduct($id, array $product);

    public function getCategories();
}