<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSendWater extends Model
{
    const STATUS_IS_TRUE = 1;
    const STATUS_IS_FALSE = 0;
    const STATUS_IS_ACTIVE_FALSE = 2;

    const SHARE_TYPE_APP = 1;
    const SHARE_TYPE_WEIXIN = 2;

    protected $table = 'user_send_water';

    /**
     * 过期时间
     */
    public static function getOverdueDate()
    {
        return (time() + strtotime("+48 hour"));
    }
}