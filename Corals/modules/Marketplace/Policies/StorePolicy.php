<?php

namespace Corals\Modules\Marketplace\Policies;

use Corals\User\Models\User;
use Corals\Modules\Marketplace\Models\Store;

class StorePolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Marketplace::store.view')) {
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
        return $user->can('Marketplace::store.create');
    }

    /**
     * @param User $user
     * @param Store $store
     * @return bool
     */
    public function update(User $user, Store $store)
    {

        if ($user->can('Marketplace::store.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Store $store
     * @return bool
     */
    public function destroy(User $user, Store $store)
    {
        if ($user->can('Marketplace::store.delete')) {
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
