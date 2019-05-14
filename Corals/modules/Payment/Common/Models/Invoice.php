<?php

namespace Corals\Modules\Payment\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Traits\ModelUniqueCode;
use Corals\Foundation\Transformers\PresentableTrait;
use Corals\User\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends BaseModel
{
    use PresentableTrait, LogsActivity, ModelUniqueCode;

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'payment_common.models.invoice';

    protected static $logAttributes = [];

    protected $guarded = ['id'];

    protected $casts = [
        'extras' => 'array',
        'is_sent' => 'boolean',
    ];

    public function scopeMyInvoices($query)
    {
        return $query->where('user_id', user()->id);
    }

    public function markAsPaid()
    {
        $this->fill(['status' => 'paid'])->save();
    }

    public function markAsFailed()
    {
        $this->fill(['status' => 'failed'])->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the owning invoicable models.
     */
    public function invoicable()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }


    public function getPdfFileName($fullPath = false)
    {
        $fileName = "invoice_{$this->code}.pdf";

        if ($fullPath) {
            if (!\File::exists(storage_path('app/attachments/invoices/' . $this->id))) {
                \File::makeDirectory(storage_path('app/attachments/invoices/' . $this->id), 0755, true);
            }

            $fileName = storage_path('app/attachments/invoices/' . $this->id . '/' . $fileName);
        }

        return $fileName;
    }
}
