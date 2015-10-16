<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    const STATUS_FALSE = 1;
    const STATUS_TRUE = 0;

    protected $table = 'user_verify';

    //过期时间30分钟
    public function getExpiredTime()
    {
        return getenv('VERIFY_EXPIRED_TIME');
    }

    //短信提交地址
    public function getSendMsgUrl($phone, $content)
    {
        $url['sname'] = getenv('SEND_MSG_SNAME');
        if (!empty($url['sname'])) {
            return false;
        }
        $url['spwd'] = getenv('SEND_MSG_SPWD');
        if (!empty($url['spwd'])) {
            return false;
        }
        $url['scorpid'] = getenv('SEND_MSG_SCORPID');
        $url['sprdid'] = getenv('SEND_MSG_SPRDID');
        if (!empty($url['sprdid'])) {
            return false;
        }
        $url['sdst'] = $phone;
        $url['smsg'] = $content;
        $curl = getenv('SEND_MSG_URL') . '?' . http_build_query($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        \Log::info($output);
        return true;
    }
}