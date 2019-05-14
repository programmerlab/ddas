<?php

namespace Corals\Modules\Marketplace\Policies;

use Corals\User\Models\User;
use Corals\Modules\Marketplace\Models\SKU;

class SKUPolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Marketplace::product.view')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Marketplace::product.create');
    }

    /**
     * @param User $user
     * @param SKU $sku
     * @return bool
     */
    public function update(User $user, SKU $sku)
    {
        if ($user->can('Marketplace::product.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param SKU $sku
     * @return bool
     */
    public function destroy(User $user, SKU $sku)
    {
        if ($user->can('Marketplace::product.delete')) {
            return true;
        }
        return false;
    }


    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if ($user->hasPermissionTo('Administrations::admin.marketplace')) {
            return true;
        }

        return null;
    }
}
