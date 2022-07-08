<?php

namespace Inensus\SteamaMeter\Models;
use App\Models\BaseModel;

class SteamaSmsSetting extends BaseModel
{
    protected $table = 'steama_sms_settings';

    public function setting()
    {
        return $this->morphOne(SteamaSetting::class, 'setting');
    }
}
