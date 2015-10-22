<?php namespace App\Application;

use App\Model\GetShopWaterLog;
use App\Model\SystemHasWater;
use App\Model\UserBase;
use App\Model\UserBlackWater;
use App\Model\UserCompanyExtend;
use App\Model\UserFinancial;
use App\Model\UserImage;
use App\Model\UserRelationship;
use App\Model\UserSendWater;
use Carbon\Carbon;
use \Queue;

class WaterService
{
    /**
     * 亲水包领取
     * @param $params
     * @param $user_id
     * @return array
     */
    public function bagGet($params, $user_id)
    {
        if (!$params->get('bagID')) {
            return [
                'status' => false,
                'message' => '领取了一个不存在的亲水包!',
                'info' => [],
            ];
        }
        $result = UserSendWater::where('id', $params->get('bagID'))->where('accept_user_id', $user_id)->first();
        if (empty($result)) {
            return [
                'status' => false,
                'message' => '亲水包不存在!',
                'info' => [],
            ];
        }
        if ($result->status == UserSendWater::STATUS_IS_FALSE && strtotime($result->overdue_date) > time()) {
            $financial_result = UserFinancial::where('user_id', $user_id)->first();
            if (empty($financial_result)) {
                $user_financiel = new UserFinancial();
                $user_financiel->user_id = $user_id;
                $user_financiel->water_count = $result->water_count;
                $user_financiel->save();
            } else {
                UserFinancial::where('user_id', $user_id)->update(['water_count' => ($financial_result->water_count + $result->water_count)]);
            }
            $bool = UserSendWater::where('id', $params->get('bagID'))->update(['status' => UserSendWater::STATUS_IS_TRUE]);
            if ($bool) {
                return [
                    'status' => true,
                    'message' => 'success',
                    'info' => [],
                ];
            } else {
                return [
                    'status' => false,
                    'message' => '领取失败!',
                    'info' => [],
                ];
            }
        }
        return [
            'status' => false,
            'message' => '该亲水包已经飞走了!',
            'info' => [],
        ];

    }

    /**
     * 亲水包发送
     * @param $params
     * @param $user_id
     * @return array
     */
    public function bagSend($params, $user_id)
    {
        $water_result = UserFinancial::where('user_id', $user_id)->first();
        if (empty($water_result) || $water_result->water_count < $params->get('money')) {
            return [
                'status' => false,
                'message' => '你的亲水值不够!',
                'info' => [],
            ];
        }
        $financial_bool = UserFinancial::where('user_id', $user_id)->update(
            [
                'water_count' => ($water_result->water_count - $params->get('money')),
                'send_water' => ($water_result->send_water + $params->get('money')),
            ]
        );
        if ($financial_bool) {
            $user_send_water = new UserSendWater();
            $user_send_water->user_id = $user_id;
            $user_send_water->water_count = $params->get('money');
            $user_send_water->accept_user_id = $params->get('getID');
            $user_send_water->overdue_date = UserSendWater::getOverdueDate();
            $bool = $user_send_water->save();
            if ($bool) {
                $time_out = Carbon::now()->addHour(getenv('TIMEOUT_HOUR'));
                Queue::later($time_out, 'App\Queue\RecyclingQueue', ['send_id' => $user_send_water->id], 'send_water');
                return [
                    'status' => true,
                    'message' => 'success',
                    'info' => [],
                ];
            }
        }
        return [
            'status' => false,
            'message' => '发送亲水包失败!',
            'info' => [],
        ];
    }

    /**
     * 亲水列表
     * @param $params
     * @param $user_id
     * @return array
     */
    public function bagList($params, $user_id)
    {
        $where['accept_user_id'] = $user_id;
        if ($params->get('status')) {
            $where['status'] = $params->get('status');
        }
        $page = $params->get('page') ?: 1;
        $count = $params->get('count') ?: 10;
        $user_send_water = new UserSendWater();
        $result = $user_send_water->where($where)->skip(($page - 1) * $count)->take($count)->get()->toArray();
        $send_water = $user_send_water->where($where)->count();
        $next_page = ((($page - 1) * $count) >= $send_water) ? $page : $page + 1;
        $pager = [
            'page' => $page,
            'count' => $count,
            'total' => $send_water,
            'next' => $next_page,
        ];
        if (empty($result)) {
            return [
                'status' => true,
                'message' => 'success',
                'info' => [],
                'pager' => $pager,
            ];
        }
        $user_ids = [];
        foreach ($result as $value) {
            $user_ids[] = $value['user_id'];
        }
        $user_result = UserBase::whereIn('user_id', array_unique($user_ids))->get()->toArray();
        $user_list = [];
        foreach ($user_result as $user_result_val) {
            $user_list[$user_result_val['user_id']] = $user_result_val['user_name'] ?: '匿名';
        }
        $list = [];
        foreach ($result as $value) {
            $list[] = [
                'bag_id' => $value['id'],
                'water_num' => $value['water_count'],
                'from_id' => $value['user_id'],
                'from_name' => $user_list[$value['user_id']],
                'create_time' => $value['created_at'],
                'get_time' => $value['updated_at'],
                'status' => $value['status'],
            ];
        }
        return [
            'status' => true,
            'message' => 'success',
            'info' => $list,
            'pager' => $pager,
        ];
    }

    /**
     * 查询店铺信息
     * @param $params
     * @param $user_id
     * @return array
     */
    public function mapDetail($params, $user_id)
    {
        if (!$params->get('storeId')) {
            return [
                'status' => false,
                'message' => '查询店铺不存在!',
                'info' => [],
            ];
        }
        $result = UserCompanyExtend::where('user_id', $params->get('storeId'))->first();
        if (!$result) {
            return [
                'status' => false,
                'message' => '查询店铺不存在!',
                'info' => [],
            ];
        }
        $data = [
            'store_id' => $result->user_id,
            'store_name' => $result->user_company_name,
            'address' => $result->user_address,
            'information' => $result->user_desc,
        ];
        $user_financial_result = UserFinancial::where('user_id', $user_id)->first();
        if ($user_financial_result) {
            $data['left_num'] = $user_financial_result->water_count;
            $data['send_num'] = $user_financial_result->send_water;
        } else {
            $data['left_num'] = $data['send_num'] = 0;
        }
        $user_image_result = UserImage::where('user_id', $params->get('storeId'))->Head()->first();
        if ($user_image_result) {
            $data['info_image'] = $user_image_result->path();
        } else {
            $data['info_image'] = [];
        }
        return [
            'status' => true,
            'message' => 'success',
            'info' => $data,
        ];
    }

    /**
     * 水地图亲水包获取
     * @param $params
     * @param $user_id
     * @return Array
     */
    public function mapBag($params, $user_id)
    {
        $user_financial = new UserFinancial();
        $result = $user_financial->where('user_id', $params->get('storeID'))->first();
        if (empty($result)) {
            return [
                'status' => false,
                'message' => '店铺ID不存在!',
                'info' => [],
            ];
        }
        if ($result->giving == 0 || $result->water_count < $result->giving) {
            return [
                'status' => false,
                'message' => '已经领取完了!',
                'info' => [],
            ];
        }
        $start_now_time = Carbon::now()->format('Y-m-d') . ' 00:00:01';
        $end_now_time = Carbon::now()->format('Y-m-d') . ' 23:23:59';
        $get_show_water_log = new GetShopWaterLog();
        $is_giving = $get_show_water_log->where('user_id', $params->get('storeID'))->where('giving_user_id', $user_id)
            ->whereBetween('created_at', [$start_now_time, $end_now_time])->first();
        if (!empty($is_giving)) {
            return [
                'status' => false,
                'message' => '今天您已经领取过,请勿重复领取!',
                'info' => [],
            ];
        }
        $bool = $user_financial->where('user_id', $params->get('storeID'))->update(
            [
                'water_count' => $result->water_count - $result->giving,
                'send_water' => $result->send_water + $result->giving,
            ]
        );
        if ($bool) {
            $my_result = $user_financial->where('user_id', $user_id)->first();
            if (!empty($my_result)) {
                $user_financial->where('user_id', $user_id)->update(
                    [
                        'water_count' => $my_result->water_count + $result->giving,
                    ]
                );
            } else {
                $user_financial->user_id = $user_id;
                $user_financial->water_count = $result->giving;
                $user_financial->save();
            }

            //给与推荐给我相关的人分享一半的值
            $user_relationship = new UserRelationship();
            $user_relationship_rt = $user_relationship->where('guest_id', $user_id)->first();
            $has_guest = false;
            if (!empty($user_relationship_rt) && ($result->water_count - $result->giving - $result->giving * UserRelationship::getGuestRate() >= 0)) {
                $has_guest = true;
                $be_guest_result = $user_financial->where('user_id', $params->get('storeID'))->first();
                $user_financial->where('user_id', $params->get('storeID'))->update(
                    [
                        'water_count' => $result->water_count - $result->giving * UserRelationship::getGuestRate(),
                        'send_water' => $result->send_water + $result->giving * UserRelationship::getGuestRate(),
                    ]
                );
                if (!empty($be_guest_result)) {
                    $user_financial->where('user_id', $user_relationship_rt->user_id)->update(
                        [
                            'water_count' => $my_result->water_count + $result->giving * UserRelationship::getGuestRate(),
                        ]
                    );
                } else {
                    $user_financial->user_id = $user_relationship_rt->user_id;
                    $user_financial->water_count = $result->giving * UserRelationship::getGuestRate();
                    $user_financial->save();
                }
                $get_show_water_log->user_id = $params->get('storeID');
                $get_show_water_log->water_count = $result->giving * UserRelationship::getGuestRate();
                $get_show_water_log->giving_user_id = $user_relationship_rt->user_id;
                $get_show_water_log->type = GetShopWaterLog::GUEST_GET_TYPE;
                $get_show_water_log->save();
            }

            $guest_val = 0;
            if ($has_guest == true) {
                $guest_val = UserRelationship::getGuestRate();
            }

            //再扣除公益值
            if ($result->water_count - $result->giving - $result->giving * $guest_val - $result->giving * SystemHasWater::getRate() >= 0) {
                $user_financial->where('user_id', $params->get('storeID'))->update(
                    [
                        'water_count' => $result->water_count - $result->giving * SystemHasWater::getRate(),
                        'send_water' => $result->send_water + $result->giving * SystemHasWater::getRate(),
                    ]
                );
                $system_has_water = new SystemHasWater();
                $system_has_water->business_id = $params->get('storeID');
                $system_has_water->sys_water_count = $result->giving * SystemHasWater::getRate();
                $system_has_water->rate = SystemHasWater::getRate();
                $system_has_water->save();
                $get_show_water_log->user_id = $params->get('storeID');
                $get_show_water_log->water_count = $result->giving * SystemHasWater::getRate();
                $get_show_water_log->giving_user_id = $user_relationship_rt->user_id;
                $get_show_water_log->type = GetShopWaterLog::SYS_GET_TYPE;
                $get_show_water_log->save();
            }

            $get_show_water_log->user_id = $params->get('storeID');
            $get_show_water_log->water_count = $result->giving;
            $get_show_water_log->giving_user_id = $user_id;
            $get_show_water_log->save();
            return [
                'status' => false,
                'message' => '领取成功!',
                'info' => [],
            ];
        }
        return [
            'status' => false,
            'message' => '领取失败!',
            'info' => [],
        ];
    }

    /**
     * 水地图列表
     * @param $params
     * @return array
     */
    public function mapList($params)
    {
        if ($params->get('longitude') && $params->get('latitude')) {
//            $squares = $this->returnSquarePoint($params->get('longitude'), $params->get('latitude'), $params->get('radius') ?: null);
//            $result = UserCompanyExtend::where('user_company_lat', '>', '0')
//                ->where('user_company_lat', '>=', $squares['right-bottom']['lat'])
//                ->where('user_company_lat', '<=', $squares['left-top']['lat'])
//                ->where('user_company_lng', '>=', $squares['left-top']['lng'])
//                ->where('user_company_lng', '<=', $squares['right-bottom']['lng'])
//                ->get();
            $user_company_extend = new UserCompanyExtend();
            $result = $user_company_extend->get();
            if (empty($result)) {
                return [
                    'status' => true,
                    'message' => '获取成功!',
                    'info' => [],
                ];
            }
            $user_ids = [];
            foreach ($result as $result_val) {
                $user_ids[] = $result_val->user_id;
            }
            $user_images_result = UserImage::whereIn('user_id', $user_ids)->Head()->get();
            $images = [];
            if (!empty($user_images_result)) {
                foreach ($user_images_result as $user_images_result_val) {
                    $images[$user_images_result_val->user_id] = $user_images_result_val->path();
                }
            }
            $user_financial_result = UserFinancial::whereIn('user_id', $user_ids)->get();
            $financial = [];
            if (!empty($user_financial_result)) {
                foreach ($user_financial_result as $user_financial_result_val) {
                    $financial[$user_financial_result_val->user_id] = $user_financial_result_val->water_count;
                }
            }
            $data = [];
            foreach ($result as $result_value) {
                $data[] = [
                    'store_id' => $result_value->user_id,
                    'store_name' => $result_value->user_company_name,
                    'image_url' => isset($images[$result_value->user_id]) ? $images[$result_value->user_id] : '',
                    'left_num' => isset($financial[$result_value->user_id]) ? $financial[$result_value->user_id] : 0,
                    'latitude' => $result_value->user_company_lat,
                    'longitude' => $result_value->user_company_lng,
                    'distance' => $this->getDistance(
                        $params->get('latitude'),
                        $params->get('longitude'),
                        $result_value->user_company_lat,
                        $result_value->user_company_lng
                    )
                ];
            }
            return [
                'status' => true,
                'msg' => 'success',
                'info' => $data,
            ];
        } else {
            return [
                'status' => false,
                'message' => '缺少经纬度参数!',
                'info' => [],
            ];
        }
    }

    /**
     * 获取周围坐标
     * @param $lng
     * @param $lat
     * @param float $distance
     * @return array
     */
    private function returnSquarePoint($lng, $lat, $distance = 0.5)
    {
        $earthRadius = 6378138;
        $dlng = 2 * asin(sin($distance / (2 * $earthRadius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance / $earthRadius;
        $dlat = rad2deg($dlat);
        return array(
            'left-top' => array('lat' => $lat + $dlat, 'lng' => $lng - $dlng),
            'right-top' => array('lat' => $lat + $dlat, 'lng' => $lng + $dlng),
            'left-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng - $dlng),
            'right-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng + $dlng)
        );
    }

    /**
     * 计算两个坐标的直线距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float
     */
    private function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6378138; //近似地球半径米
        // 转换为弧度
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        // 使用半正矢公式  用尺规来计算
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }

    /**
     * 水银行信息
     * @param $user_id
     * @return array
     */
    public function bankInfo($user_id)
    {
        $data = [];
        $user_black_water = UserBlackWater::where('user_id', $user_id)->first();
        $data['water_num'] = empty($user_black_water) ? 0 : $user_black_water->black_water;
        $user_financial = new UserFinancial();
        $data['person_water'] = $user_financial->sum('water_count');
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        $data['protect_num'] = ($user_financial_result ? $user_financial_result->water_count : 0);
        return [
            'status' => true,
            'message' => 'success',
            'info' => $data,
        ];
    }
}