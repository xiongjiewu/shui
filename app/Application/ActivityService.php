<?php namespace App\Application;

use App\Model\Activity;
use App\Model\ActivityDonationsLog;
use App\Model\ActivityFundraising;
use App\Model\ActivityImage;
use App\Model\UserFinancial;
use App\Model\UserFocus;
use App\Model\UserSupport;

class ActivityService
{
    /**
     * 取消关注或者关注
     * @param $is_active 1-开启 0-关闭
     * @param $params
     * @param $user_id
     * @param $is_active
     * @return array
     */
    public function activeFocus($params, $user_id, $is_active)
    {
        /** @var $user_focus \App\Model\UserFocus */
        $user_focus = new UserFocus();
        $result = $user_focus->where('activity_id', $params->get('activeID'))->where('user_id', $user_id)->first();
        if (empty($result)) {
            $user_focus->user_id = $user_id;
            $user_focus->activity_id = $params->get('activeID');
            $user_focus->is_active = $is_active;
            $bool = $user_focus->save();
        } else {
            $bool = $user_focus->where('activity_id', $params->get('activeID'))->where('user_id', $user_id)
                ->update(['is_active' => $is_active]);
        }
        if ($bool) {
            return [
                'status' => true,
                'message' => 'success',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '系统错误!',
                'info' => [],
            ];
        }
    }

    /**
     * 支持
     * @param $params
     * @param $user_id
     * @return array
     */
    public function activeSupport($params, $user_id)
    {
        $activity_id = $params->get('activityID');
        if (empty($activity_id)) {
            return [
                'status' => false,
                'message' => '文章ID不能为空!',
                'info' => [],
            ];
        }
        $user_support = new UserSupport();
        $rt = $user_support->where('user_id', $user_id)->where('activity_id', $activity_id)->first();
        if (!empty($rt)) {
            return [
                'status' => false,
                'message' => '请勿重复支持!',
                'info' => [],
            ];
        } else {
            $user_support->user_id = $user_id;
            $user_support->activity_id = $activity_id;
            $user_support->save();
            $activity = new Activity();
            $a_rt = $activity->where('activity_id', $activity_id)->first();
            $r = $activity->where('activity_id', $activity_id)->update(
                [
                    'focus_count' => ($a_rt->focus_count + 1)
                ]
            );
            if (!empty($r)) {
                return [
                    'status' => true,
                    'message' => '谢谢支持!',
                    'info' => [],
                ];
            } else {
                return [
                    'status' => false,
                    'message' => '系统旅行去了，请重试!',
                    'info' => [],
                ];
            }
        }
    }

    /**
     * 公益活动列表
     * @param $params
     * @param $user_id
     * @return array
     */
    public function showList($params, $user_id)
    {
        $page = $params->get('page') ?: 1;
        $count = $params->get('count') ?: 10;
        /** @var $activity \App\Model\Activity */
        $activity = new Activity();
        $activity_result = $activity->StatusOk()->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $count)->take($count)->get()->toArray();
        $activity_count = $activity->StatusOk()->count();
        $next_page = ((($page - 1) * $count) >= $activity_count) ? $page : $page + 1;
        $pager = [
            'page' => $page,
            'count' => $count,
            'total' => $activity_count,
            'next' => $next_page,
        ];
        if (empty($activity_result)) {
            return [
                'status' => true,
                'message' => '获取成功!',
                'info' => [],
                'pager' => $pager,
            ];
        }
        $list = [];
        $activity_id_list = [];
        foreach ($activity_result as $activity_result_v) {
            array_push($activity_id_list, $activity_result_v['activity_id']);
            $list[] = [
                'active_id' => $activity_result_v['activity_id'],
                'title' => $activity_result_v['title'],
                'content' => $activity_result_v['desc'],
                'create_time' => $activity_result_v['created_at'],
                'is_focus' => UserFocus::IS_ACTIVE_FALSE,
                'support' => $activity_result_v['focus_count'],
                'image_url' => ActivityImage::defaultImage(),
            ];
        }
        //获得关注信息
        $user_focus = UserFocus::whereIn('activity_id', array_unique($activity_id_list))
            ->where('user_id', $user_id)->IsActiveTrue()->get()->toArray();
        $activity_id_is_true = [];
        if (!empty($user_focus)) {
            foreach ($user_focus as $user_focus_v) {
                array_push($activity_id_is_true, $user_focus_v['activity_id']);
            }
        }
        //获得图片
        $activity_image_result = ActivityImage::whereIn('activity_id', $activity_id_list)->PIC()
            ->groupBy('activity_id')->get();
        $activity_image_info = [];
        if (!empty($activity_image_result)) {
            foreach ($activity_image_result as $activity_image_result_v) {
                $activity_image_info[$activity_image_result_v->activity_id] = $activity_image_result_v->path();
            }
        }
        foreach ($list as &$v) {
            if (in_array($v['active_id'], $activity_id_is_true)) {
                $v['is_focus'] = UserFocus::IS_ACTIVE_TRUE;
            }
            if (isset($activity_image_info[$v['active_id']])) {
                $v['image_url'] = $activity_image_info[$v['active_id']];
            }
        }

        return [
            'status' => true,
            'message' => '获取成功!',
            'info' => $list,
            'pager' => $pager
        ];
    }

    /**
     * 公益活动详情
     * @param $params
     * @param $user_id
     * @return array
     */
    public function showDetail($params, $user_id)
    {
        if (!$params->get('activeID')) {
            return [
                'status' => false,
                'message' => '活动ID不能为空!',
                'info' => [],
            ];
        }
        /** @var $activity \App\Model\Activity */
        $activity = new Activity();
        $activity_result = $activity->where('activity_id', $params->get('activeID'))->StatusOk()->first();
        if (empty($activity_result)) {
            return [
                'status' => false,
                'message' => '活动不存在!',
                'info' => [],
            ];
        }
        $data = [];
        $data['active_id'] = $params->get('activeID');
        $data['title'] = $activity_result->title;
        $data['content'] = $activity_result->desc;
        $data['create_time'] = (String)$activity_result->created_at;
        $data['is_focus'] = UserFocus::userIsFocus($params->get('activeID'), $user_id);
        $data['support'] = $activity_result->focus_count;
        $data['image_url'] = ActivityImage::getImages($params->get('activeID'));
        $data['video_url'] = ActivityImage::getImages($params->get('activeID'), ActivityImage::TYPE_IMAGE_IS_GIF);
        $data['like_url'] = $activity_result->url;
        $data['left_money'] = 0;
        $data['now_money'] = 0;
        $data['people_num'] = 0;
        $fundraising = ActivityFundraising::where('activity_id', $params->get('activeID'))->first();
        if (!empty($fundraising)) {
            $data['left_money'] = $fundraising->total_amount_price;
            $data['now_money'] = $fundraising->existing_price;
            $data['people_num'] = $fundraising->fundraising_count;
        }
        return [
            'status' => true,
            'message' => '获取成功!',
            'info' => $data,
        ];
    }

    /**
     * 公益捐款
     * @param $params
     * @param $user_id
     * @return array
     */
    public function activeDonations($params, $user_id)
    {
        if (!$params->get('activeID')) {
            return [
                'status' => false,
                'message' => '活动ID不能为空!',
                'info' => [],
            ];
        }
        if (!$params->get('money')) {
            return [
                'status' => false,
                'message' => '金额不能为空!',
                'info' => [],
            ];
        }
        //金额转换亲水值
        $money_to_water = ($params->get('money') * ActivityDonationsLog::getRate());
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        if ($user_financial_result->water_count < $money_to_water) {
            return [
                'status' => false,
                'message' => '你的亲水值不够!',
                'info' => [],
            ];
        }
        $bool = $user_financial->where('user_id', $user_id)->update(
            [
                'water_count' => ($user_financial_result->water_count - $money_to_water),
                'send_water' => ($user_financial_result->send_water + $money_to_water),
            ]
        );
        if ($bool) {
            $activity_fundraising = new ActivityFundraising();
            $activity_fundraising_result = $activity_fundraising->where('activity_id', $params->get('activeID'))->first();
            $activity_fundraising->where('activity_id', $params->get('activeID'))->update(
                [
                    'fundraising_count' => ($activity_fundraising_result->fundraising_count + 1),
                    'existing_price' => ($activity_fundraising_result->existing_price + $params->get('money')),
                ]
            );
            //记录日志
            $activity_donations_log = new ActivityDonationsLog();
            $activity_donations_log->active_id = $params->get('activeID');
            $activity_donations_log->user_id = $user_id;
            $activity_donations_log->water_count = $money_to_water;
            $activity_donations_log->price = $params->get('money');
            $activity_donations_log->rate = ActivityDonationsLog::getRate();
            $activity_donations_log->save();
            return [
                'status' => true,
                'message' => '捐款成功!',
                'info' => [],
            ];
        }
        return [
            'status' => false,
            'message' => '系统错误!',
            'info' => [],
        ];
    }

    public function getList()
    {
        $activity = new Activity();
        $activities = $activity->paginate(10);
        if ($activities->isEmpty()) {
            return [];
        }

        $result = [];
        foreach ($activities as $activity) {
            $info = $activity->toArray();
            $info['status_text'] = ($activity->status == 1) ? '正常' : '关闭';
            $info['action_text'] = ($activity->status == 1) ? '关闭' : '打开';
            $result[] = $info;
        }
        return [
            'list' => $result,
            'obj' => $activities
        ];
    }

    public function changeStatus($id, $status)
    {
        if (Activity::where('activity_id', $id)->update(['status' => $status])) {
            return true;
        }
        return false;
    }
}
