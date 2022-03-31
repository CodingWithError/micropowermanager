<?php

namespace App\Models;

use App\Models\Meter\MeterTariff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeOfUsage extends BaseModel
{
    protected $connection = 'test_company_db';
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(MeterTariff::class);
    }
}
