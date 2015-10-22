<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GetShopWaterLog extends Model
{
    const USER_GET_TYPE = 1;
    const SYS_GET_TYPE = 2;
    const GUEST_GET_TYPE = 3;

    protected $table = 'get_shop_water_log';
}
