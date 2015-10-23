<?php namespace App\Queue;

use App\Model\UserFinancial;
use App\Model\UserSendWater;

class RecyclingQueue
{
    /**
     * @param $job
     * @param $data
     */
    public function fire($job, $data)
    {
        $job->delete();
        if (!empty($data['send_id'])) {
            $user_send_water = new UserSendWater();
            $result = $user_send_water->where('id', $data['send_id'])->first();
            if (!empty($result) && $result->status == UserSendWater::STATUS_IS_FALSE) {
                $user_send_water->where('id', $data['send_id'])->update(
                    [
                        'status' => UserSendWater::STATUS_IS_ACTIVE_FALSE,
                    ]
                );
                $user_financial_result = UserFinancial::where('user_id', $result->user_id)->first();
                UserFinancial::where('user_id', $result->user_id)->update(
                    [
                        'water_count' => ($user_financial_result->water_count + $result->water_count),
                        'send_water' => ($user_financial_result->send_water - $result->water_count)
                    ]
                );
            }
        }
    }
}