<?php

namespace App\Providers;

use App\Contracts\Store;
use App\Importer\WooCommerceStore;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Store::class, function () {
            $config = config('services.woo-commerce');

            return new WooCommerceStore($config['url'], $config['consumer_key'], $config['consumer_secret']);
        });
    }
}
