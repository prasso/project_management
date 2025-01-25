<?php

namespace Prasso\ProjectManagement\Models;

class Client extends BaseModel
{
    protected $table = 'clients';

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
