<?php namespace App\Application\User;

use App\Model\UserFinancial;
use App\Model\UserSendWater;
use Illuminate\Support\Facades\DB;

class WaterService
{
    /**
     * 亲水包领取
     * @param $params
     * @return array
     */
    public function bagGet($params)
    {
        if (!isset($params['bagID'])) {
            return [
                'status' => false,
                'msg' => '领取了一个不存在的亲水包!',
                'info' => [],
            ];
        }
        $result = UserSendWater::where('id', $params['bagID'])->where('accept_user_id', $params['userID'])->first();
        if (empty($result)) {
            return [
                'status' => false,
                'msg' => '亲水包不存在!',
                'info' => [],
            ];
        }
        if ($result->status == UserSendWater::STATUS_IS_FALSE && strtotime($result->overdue_date) > time() && $result->is_active == UserSendWater::STATUS_IS_FALSE) {
            DB::transaction(function ($params, $result) {
                try {
                    $financial_result = UserFinancial::where('user_id', $params['userID'])->first();
                    if (empty($financial_result)) {
                        $user_financiel = new UserFinancial();
                        $user_financiel->user_id = $params['userID'];
                        $user_financiel->water_count = $result->water_count;
                        $user_financiel->save();
                    } else {
                        UserFinancial::where('user_id', $params['userID'])->update(['water_count' => ($financial_result->water_count + $result->water_count)]);
                    }
                    UserSendWater::where('id', $params['bagID'])->update(['status' => UserSendWater::STATUS_IS_TRUE]);
                } catch (\Exception $e) {
                    return [
                        'status' => false,
                        'msg' => '领取失败!',
                        'info' => [],
                    ];
                }
            });
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => [],
        ];
    }

    /**
     * 亲水包发送
     * @param $params
     * @return array
     */
    public function bagSend($params)
    {
        $water_result = UserFinancial::where('user_id', $params['userID'])->first();
        if (empty($water_result) || $water_result->water_count < $params['money']) {
            return [
                'status' => false,
                'msg' => '你的亲水值不够!',
                'info' => [],
            ];
        }
        try {
            //开启事务
            DB::transaction(function ($params, $water_result) {
                UserFinancial::where('user_id', $params['userID'])->update(['water_count' => ($water_result->water_count - $params['money'])]);
                $user_send_water = new UserSendWater();
                $user_send_water->user_id = $params['userID'];
                $user_send_water->water_count = $params['money'];
                $user_send_water->accept_user_id = $params['getID'];
                $user_send_water->overdue_date = UserSendWater::getOverdueDate();
                $user_send_water->save();
            });
        } catch (\Exception $e) {
            return [
                'status' => false,
                'msg' => '发送亲水包失败!',
                'info' => [],
            ];
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => [],
        ];
    }

    /**
     * 亲水列表
     * @param $params
     * @return array
     */
    public function bagList($params)
    {
        $where['status'] = isset($params['status']) ?: UserSendWater::STATUS_IS_FALSE;
        $where['accept_user_id'] = $params['userID'];
        $result = UserSendWater::where($where)->get()->toArray();
        if (empty($result)) {
            return [
                'status' => true,
                'msg' => 'success',
                'info' => [],
            ];
        }
        $user_ids = [];
        foreach ($result as $value) {
            $user_ids[] = $value['user_id'];
        }
        $user_result = UserBase::whereIn('id', $user_ids)->get()->toArray();
        $user_list = [];
        foreach ($user_result as $user_result_val) {
            $user_list[$user_result_val['id']] = $user_result_val['user_name'];
        }
        $list = [];
        foreach ($result as $value) {
            $list[] = [
                'bag_id' => $value['id'],
                'water_num' => $value['water_count'],
                'from_id' => $value['user_id'],
                'from_name' => isset($user_list[$value['user_id']]) ? $user_list[$value['user_id']] : '',
                'create_time' => $value['created_at'],
                'get_time' => $value['updated_at'],
                'status' => $value['status'],
            ];
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => $list,
        ];
    }
}