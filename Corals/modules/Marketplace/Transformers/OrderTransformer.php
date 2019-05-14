<?php

namespace Corals\Modules\Marketplace\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Marketplace\Models\Order;

class OrderTransformer extends BaseTransformer
{
    public function __construct()
    {
        $this->resource_url = config('marketplace.models.order.resource_url');

        parent::__construct();
    }

    /**
     * @param Order $order
     * @return array
     * @throws \Throwable
     */
    public function transform(Order $order)
    {
        $actions = ['edit' => '', 'delete' => ''];

        if (user()->hasPermissionTo('Marketplace::order.update')) {
            $actions['change_status'] = [
                'icon' => 'fa fa-fw fa-edit',
                'href' => url($this->resource_url . '/' . $order->hashed_id . '/edit'),
                'label' => trans('Marketplace::labels.order.update_order'),
                'class' => 'modal-load',
                'data' => [
                    'title' => 'Update Order'
                ]

            ];
        }

        if (user()->can('create', \Corals\Modules\Messaging\Models\Discussion::class)) {
            $actions['contact'] = [
                'icon' => 'fa fa-fw fa-envelope-o',
                'href' => url('messaging/discussions/create?user=' . $order->user->hashed_id ),
                'label' => trans('Marketplace::labels.order.contact_buyer'),
                'data' => [
                ]

            ];
        }






        $currency = strtoupper($order->currency);

        $levels = [
            'pending' => 'info',
            'processing' => 'success',
            'completed' => 'primary',
            'failed' => 'danger',
            'canceled' => 'warning'
        ];

        if (\Store::isStoreAdmin()) {
            $user_id = "<a target='_blank' href='" . url('users/' . $order->user->hashed_id) . "'> {$order->user->full_name}</a>";

        } else {
            $user_id = $order->user->full_name;
        }


        $payment_levels = [
            'pending' => 'info',
            'paid' => 'success',
            'canceled' => 'danger',
            'refunded' => 'warning'
        ];


        $payment_status = $order->billing['payment_status'] ?? '';


        return [
            'status' => formatStatusAsLabels($order->status, ['level' => $levels[$order->status], 'text' => trans('Marketplace::status.order.' . $order->status)]),
            'payment_status' => $payment_status ? formatStatusAsLabels($payment_status, ['level' => $payment_levels[$payment_status], 'text' => trans('Marketplace::status.payment.' . $payment_status)]) : ' -  ',
            'user_id' => $user_id,

            'order_number' => '<a  href="' . url($this->resource_url . '/' . $order->hashed_id) . '">' . $order->order_number . '</a>',
            'id' => $order->id,
            'currency' => $currency,
            'amount' => currency()->format($order->amount, $currency),
            'store' => $order->store ? '<a target="_blank" href="' . $order->store->getUrl() . '">' . $order->store->name . '</a>' : '-',
            'created_at' => format_date($order->created_at),
            'updated_at' => format_date($order->updated_at),
            'action' => $this->actions($order, $actions)
        ];
    }
}