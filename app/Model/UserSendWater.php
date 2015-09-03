<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSendWater extends Model
{
    const STATUS_IS_TRUE = 1;
    const STATUS_IS_FALSE = 0;


    const IS_ACTIVE_TRUE = 1;
    const IS_ACTIVE_FALSE = 0;

    protected $table = 'user_send_water';

    /**
     * 过期时间
     */
    public static function getOverdueDate()
    {
        return date((time() + (60 * 60 * 48)));
    }
}