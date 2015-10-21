<?php namespace App\Queue;

use App\Model\UserFinancial;
use App\Model\UserShareLog;

class RecyclingWeixinShareWaterQueue
{
    /**
     * @param $job
     * @param $data
     */
    public function fire($job, $data)
    {
        $job->delete();
        if (!empty($data['share_id'])) {
            $user_share_log = new UserShareLog();
            $user_share_log_rt = $user_share_log->where('id', $data['share_id'])->first();
            if ($user_share_log_rt->status == UserShareLog::SHARE_OK) {
                $user_share_log->where('id', $data['share_id'])->update(
                    [
                        'status' => UserShareLog::SHARE_NO
                    ]
                );
                $user_ry = ($user_share_log_rt->share_water_count - $user_share_log_rt->share_receive);
                $user_financial = new UserFinancial();
                $user_rt = $user_financial->where('user_id', $user_share_log_rt->user_id)->first();
                $user_financial->where('user_id', $user_share_log_rt->user_id)->update(
                    [
                        'water_count' => ($user_rt->water_count + $user_ry),
                        'send_water' => ($user_rt->send_water - $user_ry),
                    ]
                );
            }
        }
    }
}