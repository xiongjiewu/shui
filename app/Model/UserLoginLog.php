<?php namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    protected $table = 'user_login_log';

    public static function insert_login_log($user_id)
    {
        $date = Carbon::now()->format('Ymd');
        $result = self::where('user_id', $user_id)->where('date', $date)->first();
        if (empty($result)) {
            self::insert(
                [
                    'user_id' => $user_id,
                    'date' => $date
                ]
            );
        }
    }
}
