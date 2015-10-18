<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSendWater extends Model
{
    const STATUS_IS_TRUE = 1;
    const STATUS_IS_FALSE = 0;
    const STATUS_IS_ACTIVE_FALSE = 2;

    protected $table = 'user_send_water';

    /**
     * 过期时间
     */
    public static function getOverdueDate()
    {
        return date('Y-m-d H:i:s', strtotime("+48 hour"));
    }
}