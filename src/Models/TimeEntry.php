<?php

namespace Prasso\ProjectManagement\Models;

use Carbon\Carbon;

class TimeEntry extends BaseModel
{
    protected $table = 'time_entries';

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDurationAttribute()
    {
        if (!$this->end_time) {
            return 0;
        }

        return Carbon::parse($this->start_time)
            ->diffInSeconds(Carbon::parse($this->end_time)) / 3600;
    }

    public function getBillableAmountAttribute()
    {
        if (!$this->billable) {
            return 0;
        }

        return $this->duration * ($this->hourly_rate ?? $this->task->project->hourly_rate ?? 0);
    }
}
