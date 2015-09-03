<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserFocus extends Model
{
    //关注
    const IS_ACTIVE_TRUE = 1;
    //未关注
    const IS_ACTIVE_FALSE = 0;

    protected $table = 'user_focus';

    public function scopeIsActiveTrue($query)
    {
        return $query->where('is_active', self::IS_ACTIVE_TRUE);
    }

    public function scopeIsActiveFalse($query)
    {
        return $query->where('is_active', self::IS_ACTIVE_FALSE);
    }

    public static function userIsFocus($activity_id, $user_id)
    {
        $result = self::where('activity_id', $activity_id)->where('user_id', $user_id)->first();
        if (empty($result) || $result->is_active == self::IS_ACTIVE_FALSE) {
            return self::IS_ACTIVE_FALSE;
        } else {
            return self::IS_ACTIVE_TRUE;
        }
    }
}
