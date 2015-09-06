<?php namespace App\Application;

use App\Model\UserBase;
use App\Model\UserBlackWater;
use App\Model\UserCompanyExtend;
use App\Model\UserFinancial;
use App\Model\UserImage;
use App\Model\UserSendWater;
use Carbon\Carbon;

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
                'msg' => '领取了一个不存在的亲水包!',
                'info' => [],
            ];
        }
        $result = UserSendWater::where('id', $params->get('bagID'))->where('accept_user_id', $user_id)->first();
        if (empty($result)) {
            return [
                'status' => false,
                'msg' => '亲水包不存在!',
                'info' => [],
            ];
        }
        if ($result->status == UserSendWater::STATUS_IS_FALSE && strtotime($result->overdue_date) > time()) {
            $financial_result = UserFinancial::where('user_id', $params['userID'])->first();
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
                    'msg' => 'success',
                    'info' => [],
                ];
            } else {
                return [
                    'status' => false,
                    'msg' => '领取失败!',
                    'info' => [],
                ];
            }
        }
        return [
            'status' => false,
            'msg' => '该亲水包已经飞走了!',
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
                'msg' => '你的亲水值不够!',
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
                \Queue::later($time_out, '\App\Console\Commands\Recycling', ['send_id' => $bool->id], 'send_water');
                return [
                    'status' => true,
                    'msg' => 'success',
                    'info' => [],
                ];
            }
        }
        return [
            'status' => false,
            'msg' => '发送亲水包失败!',
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
        $where['status'] = $params->get('status') ?: UserSendWater::STATUS_IS_FALSE;
        $where['accept_user_id'] = $user_id;
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

    /**
     * 查询店铺信息
     * @param $params
     * @param $user_id
     * @return array
     */
    public function mapDetail($params, $user_id)
    {
        if (!$params->get('store_id')) {
            return [
                'status' => false,
                'msg' => '查询店铺不存在!',
                'info' => [],
            ];
        }
        $result = UserCompanyExtend::where('user_id', $params->get('store_id'))->where('type', UserBase::TYPE_BUSINESS)->first();
        if ($result) {
            return [
                'status' => false,
                'msg' => '查询店铺不存在!',
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
        $user_image_result = UserImage::where('user_id', $params->get('store_id'))->where('type', UserImage::TYPE_SHOP)->first();
        if ($user_image_result) {
            $data['info_image'] = $user_image_result->image_url;
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => $data,
        ];
    }

    /**
     * 水地图列表
     * @param $params
     * @param $user_id
     * @return array
     */
    public function mapList($params, $user_id)
    {
        if ($params->get('longitude') && $params->get($params['latitude'])) {
            $squares = $this->returnSquarePoint($params->get('longitude'), $params->get('latitude'), $params->get('radius') ?: null);
            $result = UserCompanyExtend::where('user_company_lat', '>', '0')
                ->where('user_company_lat', '>=', $squares['right-bottom']['lat'])
                ->where('user_company_lat', '<=', $squares['left-top']['lat'])
                ->where('user_company_lng', '>=', $squares['left-top']['lng'])
                ->where('user_company_lng', '<=', $squares['right-bottom']['lng'])
                ->get();
            if (empty($result)) {
                return [
                    'status' => true,
                    'msg' => '获取成功!',
                    'info' => [],
                ];
            }
            $user_ids = [];
            foreach ($result as $result_val) {
                $user_ids[] = $result_val->user_id;
            }
            $user_images_result = UserImage::whereIn('user_id', $user_ids)->where('type', UserImage::TYPE_SHOP)->first();
            $images = [];
            foreach ($user_images_result as $user_images_result_val) {
                $images[$user_images_result_val->user_id] = $user_images_result_val->path();
            }
            $user_financial_result = UserFinancial::whereIn('user_id', $user_ids)->first();
            $financial = [];
            foreach ($user_financial_result as $user_financial_result_val) {
                $financial[$user_financial_result_val->user_id] = $user_financial_result_val->water_count;
            }
            $data = [];
            foreach ($result as $result_value) {
                $data[] = [
                    'store_id' => $result_value->user_id,
                    'store_name' => $result_value->user_company_name,
                    'image_url' => isset($images[$result_value->user_id]) ?: '',
                    'left_num' => isset($financial[$result_value->user_id]) ?: 0,
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
                'msg' => '缺少经纬度参数!',
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
        $data['protect_num'] = $user_financial_result->water_count();
        return [
            'status' => true,
            'msg' => 'success',
            'info' => $data,
        ];
    }
}