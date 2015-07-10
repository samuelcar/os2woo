<?php

namespace App\Importer\Product;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed products_status
 * @property mixed products_model
 * @property mixed description
 * @property mixed specials
 * @property mixed products_price
 * @property mixed products_inStock
 * @property mixed products_image
 */
class OsProduct extends Model
{

    protected $connection = 'oscommerce';
    protected $table = 'products';
    protected $primaryKey = 'products_id';
    protected $image_path = 'http://www.motorsportsuperstore.com.au//catalog/images/';//'https://threespeedmania.files.wordpress.com/2014/01/kgrhqvomfg44lclpbr1sfermpq60_3.jpg?w=590';

    public function specials()
    {
        return $this->hasOne(OsProductSpecial::class, 'products_id');
    }

    public function description()
    {
        return $this->hasOne(OsProductDescription::class, 'products_id', 'products_id');
    }

    public function attibutes()
    {
        return $this->hasMany(OsProductAttribute::class, 'products_id', 'products_id');
    }

    public function toWooCommerce()
    {
        return [
            "title" => $this->description->products_name,
            "type" => 'simple',
            "status" => $this->products_status ? 'publish' : 'draft',
            //decided draft because can be seen in backend and not in frontend
            // default publish
            "downloadable" => false,
            "virtual" => false,
            "sku" => $this->products_model,
            "regular_price" => $this->products_price,
            "sale_price" => isset($this->specials->specials_new_products_price) ? $this->specials->specials_new_products_price : '',
            "sale_price_dates_from" => isset($this->specials->start_date) ? $this->specials->start_date : '',
            "sale_price_dates_to" => isset($this->specials->start_date) ? $this->specials->start_date : '',
            "tax_status" => 'taxable',
            //            "tax_class" => $wc_product->get_tax_class(),
            //            "managing_stock" => $wc_product->managing_stock(), //maybe optional
            "stock_quantity" => intval($this->description->products_quantity),
            "in_stock" => (bool) $this->products_inStock,
            //            "backorders" => $wc_product->backorders_allowed(),
            //            "sold_individually" => $wc_product->is_sold_individually(),
            //            //"catalog_visibility"    IDK how to get it, but it doesn't seem relevant to our system
            //            "weight" => $wc_product->get_weight(),
            //            "dimensions" => $this->getProductDimensions($wc_product),
            //            "shipping_class" => $wc_product->get_shipping_class(),
            "description" => isset($this->description->products_description) ? $this->description->products_description : '',
            "enable_html_description" => true,
            //            "short_description" => $post_product->post_excerpt,
            //            "enable_html_short_description" => true,
            "reviews_allowed" => true,
            //            "upsell_ids" => $wc_product->get_upsells(),
            //            //is an array
            //            "cross_sell_ids" => $wc_product->get_cross_sells(),
            //            "parent_id" => $wc_product->get_parent(),
            //            "categories" => $this->getProductCategories($wc_product),
            //            "tags" => $this->getProductTags($wc_product),
            'images' => [
                ['src' => $this->image_path . $this->products_image, 'position' => 0]
            ],
            //            "default_attributes" => $default_attr,
            //            //"downloads" this can be tricky to many variables. can be public?downloads	array	List of downloadable files. See Downloads Properties
            //            //download_limit	integer	Amount of times the product can be downloaded. In write-mode you can sent a blank string for unlimited re-downloads. e.g ''
            //            //download_expiry	integer	Number of days that the customer has up to be able to download the product. In write-mode you can sent a blank string for never expiry. e.g ''
            //            //download_type	string	Download type, this controls the schema. The available options are: '' (Standard Product), application (Application/Software) and music (Music)
            //            //"purchase_note"    => get_post_meta( $order->id, '_purchase_note', true) http://stackoverflow.com/questions/12801713/show-product-name-and-purchase-notes-in-my-accounts-page-in-woo-commerce
            //            "variations" => $variations,
            //            "attributes" => $this->getProductAttributes($wc_product)
            //            //"product_url"	string	Product external URL. Only for external products WRITE-ONLY
            //            //"button_text"	string	Product external button text. Only for external products WRITE-ONLY
        ];
    }
}
