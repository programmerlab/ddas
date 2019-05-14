<?php

namespace Corals\Modules\Payment\Policies;

use Corals\User\Models\User;
use Corals\Modules\Payment\Models\Invoice;

class InvoicePolicy
{

    /**
     * @param User $user
     * @param Invoice|null $invoice
     * @return bool
     */
    public function view(User $user, Invoice $invoice = null)
    {
        if ($user->can('Payment::invoices.view_all')) {
            return true;
        }

        if ($user->can('Payment::invoices.view') && $invoice) {
            if ((optional($invoice->user)->id == $user->id)) {
                return true;

            }

            if (isset($invoice->invoicable->generator) && $invoice->invoicable->generator->id == $user->id) {
                return true;
            }

        }


        return false;

    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Payment::invoices.create');
    }

    /**
     * @param User $user
     * @param Invoice $invoice
     * @return bool
     */
    public function update(User $user, Invoice $invoice)
    {
        if ($user->can('Payment::invoices.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Invoice $invoice
     * @return bool
     */
    public function destroy(User $user, Invoice $invoice)
    {
        if ($user->can('Payment::invoices.delete')) {
            return true;
        }
        return false;
    }

    public function sendInvoice(User $user, Invoice $invoice)
    {
        return in_array($invoice->status, ['pending']) && !$invoice->is_sent && ($user->can('Payment::invoices.update') || $user->hasPermissionTo('Administrations::admin.payment'));
    }


    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        $skippedAbilities = ['sendInvoice'];

        if ($user->hasPermissionTo('Administrations::admin.payment') && !in_array($ability, $skippedAbilities)) {
            return true;
        }

        return null;
    }
}
