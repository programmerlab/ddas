<?php

namespace Corals\Modules\Marketplace\Hooks;


use Corals\Modules\Marketplace\Models\Order;
use Corals\Modules\Marketplace\Models\Product;
use Corals\Modules\Marketplace\Models\Shipping;
use Corals\Modules\Marketplace\Models\Store;
use Corals\Modules\Payment\Models\Transaction;

Class HasStore
{


    /**
     * @return mixed
     */
    public function stores()
    {
        return function ($params = []) {

            $relation = $this->hasMany(Store::class, 'user_id');
            if (isset($params['getData']) && $params['getData']) {
                return $relation->getResults();
            } else {
                return $relation;

            }
        };
    }

    /**
     * @return mixed
     */
    public function transactions()
    {
        return function ($params = []) {
            $relation = $this->morphMany(Transaction::class, 'owner');
            if (isset($params['getData']) && $params['getData']) {
                return $relation->getResults();
            } else {
                return $relation;

            }
        };
    }

    /**
     * @return mixed
     */
    public function products()
    {
        return function ($params = []) {

            $relation = $this->hasManyThrough(Product::class, Store::class);
            if (isset($params['getData']) && $params['getData']) {
                return $relation->getResults();
            } else {
                return $relation;

            }
        };
    }

    /**
     * @return mixed
     */
    public function shipping_rules()
    {
        return function ($params = []) {

            $relation = $this->hasManyThrough(Shipping::class, Store::class);
            if (isset($params['getData']) && $params['getData']) {
                return $relation->getResults();
            } else {
                return $relation;

            }
        };
    }


    /**
     * @return mixed
     */
    public function storeOrders()
    {
        return function ($params = []) {

            $relation = $this->hasManyThrough(Order::class, Store::class);
            if (isset($params['getData']) && $params['getData']) {
                return $relation->getResults();
            } else {
                return $relation;

            }
        };
    }


}
