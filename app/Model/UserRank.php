<?php namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserRank extends Model
{
    protected $table = 'user_rank';

    /**
     * 默认排名
     * @return string
     */
    public static function getDefaultRank()
    {
        return (int)getenv('DEFAULT_RANK');
    }

    /**
     * 获得用户排名
     * @param $user_id
     * @return int
     */
    public static function getUserRank($user_id)
    {
        $rt = self::where('user_id', $user_id)->where('date', Carbon::now()->format('Ymd'))->first();
        if (empty($rt)) {
            return mt_rand(100, 999);
        } else {
            return $rt->rank;
        }
    }
}
