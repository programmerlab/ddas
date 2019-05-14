<?php

namespace Corals\Modules\Marketplace\Providers;

use Corals\Modules\Marketplace\Models\Coupon;
use Corals\Modules\Marketplace\Models\Product;
use Corals\Modules\Marketplace\Models\Shipping;
use Corals\Modules\Marketplace\Models\SKU;
use Corals\Modules\Marketplace\Models\Store;
use Corals\Modules\Marketplace\Policies\CouponPolicy;
use Corals\Modules\Marketplace\Policies\ProductPolicy;
use Corals\Modules\Marketplace\Policies\ShippingPolicy;
use Corals\Modules\Marketplace\Policies\SKUPolicy;
use Corals\Modules\Marketplace\Policies\StorePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class MarketplaceAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Coupon::class => CouponPolicy::class,
        Shipping::class => ShippingPolicy::class,
        Product::class => ProductPolicy::class,
        SKU::class => SKUPolicy::class,
        Store::class => StorePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}