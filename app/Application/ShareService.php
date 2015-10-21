<?php namespace App\Application;

use App\Model\UserBase;
use App\Model\UserFinancial;
use App\Model\UserShareLog;
use \Queue;

class ShareService
{
    /**
     * 创建分享链接
     * @param $params
     * @param $user_id
     * @return array
     */
    public function createShareUrl($params, $user_id)
    {
        if (!$params->get('water_num')) {
            return [
                'status' => false,
                'message' => '分享亲水值不能为空!',
                'info' => [],
            ];
        }
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        if (empty($user_financial_result)) {
            return [
                'status' => false,
                'message' => '对不起,您的亲水值为空!',
                'info' => [],
            ];
        }
        if ($user_financial_result->water_count < $params->get('water_num')) {
            return [
                'status' => false,
                'message' => '对不起,您的亲水值不够!',
                'info' => [],
            ];
        }
        $crc = crc32(md5(time()));
        $user_shar_log = new UserShareLog();
        $user_shar_log->user_id = $user_id;
        $user_shar_log->share_water_count = $params->get('water_num');
        $user_shar_log->share_time = $crc;

        if ($user_shar_log->save()) {
            $user_financial->where('user_id', $user_id)->update(
                [
                    'water_count' => ($user_financial_result->water_count - $params->get('water_num')),
                    'send_water' => ($user_financial_result->send_water + $params->get('water_num')),
                ]
            );
            $user_base = new UserBase();
            $user_base_rt = $user_base->where('user_id', $user_id)->first();
            $time_out = Carbon::now()->addHour(getenv('TIMEOUT_HOUR'));
            Queue::later($time_out, 'App\Queue\RecyclingWeixinShareWaterQueue', ['share_id' => $user_shar_log->id], 'send_water');
            return [
                'status' => true,
                'message' => '反馈成功!',
                'info' => [
                    'bag_id' => $crc,
                    'water_url' => UserShareLog::createShareUrl($crc, $user_base_rt->invite_code),
                ],
            ];
        } else {
            return [
                'status' => false,
                'message' => '对不起,系统一个人出去旅行了!',
                'info' => [],
            ];
        }
    }
}