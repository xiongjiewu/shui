<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserShareLog extends Model
{
    const SHARE_OK = 1;

    const SHARE_NO = 0;

    protected $table = 'user_share_log';

    /**
     * 获得过期秒
     * @return int
     */
    public static function getSystemTime()
    {
        return (int)(getenv('TIMEOUT_HOUR') * 3600);
    }

    /**
     * 创建分享URL
     * @param $share_time
     * @param $invite_code
     * @return string
     */
    public static function createShareUrl($share_time, $invite_code)
    {
        return getenv('SHARE_URL') . $share_time . '-' . $invite_code . '.html';
    }
}