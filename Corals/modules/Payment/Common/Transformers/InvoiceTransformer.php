<?php

namespace Corals\Modules\Payment\Common\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Payment\Models\Invoice;

class InvoiceTransformer extends BaseTransformer
{
    public function __construct()
    {
        $this->resource_url = config('payment_common.models.invoice.resource_url');

        parent::__construct();
    }

    /**
     * @param Invoice $invoice
     * @return array
     * @throws \Throwable
     */
    public function transform(Invoice $invoice)
    {
        $actions = [
            'download' => [
                'href' => url($this->resource_url . '/' . $invoice->hashed_id . '/download'),
                'label' => trans('Corals::labels.download'),
                'target' => '_blank',
                'data' => []
            ],
            'delete' => '',
        ];

        if ($invoice->status != 'paid' && \Modules::isModuleActive('corals-ecommerce')) {
            if ($invoice->invoicable && get_class($invoice->invoicable) == \Corals\Modules\Ecommerce\Models\Order::class && user()->can('payOrder', $invoice->invoicable)) {
                $actions['pay_order'] = [
                    'icon' => 'fa fa-fw fa-edit',
                    'href' => url('e-commerce/checkout/?order=' . $invoice->invoicable->hashed_id),
                    'label' => trans('Payment::labels.invoice.pay'),
                    'data' => [
                    ]
                ];
            }
        }

        if (user()->can('sendInvoice', $invoice)) {
            $actions['sendInvoice'] = [
                'href' => url($this->resource_url . '/' . $invoice->hashed_id . '/send-invoice'),
                'label' => trans('Corals::labels.send'),
                'data' => [
                    'action' => 'post',
                    'table' => '.dataTableBuilder',
                    'confirmation' => trans('Payment::messages.send_invoice'),
                ]
            ];
        }

        if (!user()->hasPermissionTo('Payment::invoices.update')) {
            $actions['edit'] = '';
        }

        $currency = strtoupper($invoice->currency);

        $levels = [
            'pending' => 'info',
            'paid' => 'success',
            'failed' => 'danger',
        ];


        return [
            'id' => $invoice->id,
            'status' => formatStatusAsLabels($invoice->status, ['level' => $levels[$invoice->status], 'text' => trans('Payment::labels.invoice.' . $invoice->status)]),
            'is_sent' => $invoice->is_sent ? '<i class="fa fa-check text-success"></i>' : '-',
            'code' => '<a href="' . $invoice->getShowURL() . '">' . $invoice->code . '</a>',
            'currency' => $currency,
            'description' => $invoice->description ? generatePopover($invoice->description) : '-',
            'due_date' => format_date($invoice->due_date),
            'sub_total' => \Payments::currency_convert($invoice->sub_total, null, $currency, true),
            'total' => \Payments::currency_convert($invoice->total, null, $currency, true),
            'user_id' => $invoice->user ? "<a href='" . url('users/' . $invoice->user->hashed_id) . "'> {$invoice->user->full_name}</a>" : "-",
            'invoicable_type' => class_basename($invoice->invoicable_type),
            'invoicable_id' => $invoice->invoicable ? $invoice->invoicable->getInvoiceReference() : '-',
            'created_at' => format_date($invoice->created_at),
            'updated_at' => format_date($invoice->updated_at),
            'action' => $this->actions($invoice, $actions)
        ];
    }
}
