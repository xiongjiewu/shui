<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SystemHasWater extends Model
{
    protected $table = 'system_has_water';

    public static function getRate()
    {
        return getenv('PUBLIC_WELFARE_GET_RATE');
    }
}