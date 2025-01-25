<?php

namespace Prasso\ProjectManagement\Models;

class InvoiceItem extends BaseModel
{
    protected $table = 'invoice_items';

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->amount = $item->quantity * $item->rate;
        });

        static::updating(function ($item) {
            $item->amount = $item->quantity * $item->rate;
        });
    }
}
