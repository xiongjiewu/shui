<?php namespace App\Application;

use App\Model\Activity;
use App\Model\ActivityFundraising;
use App\Model\ActivityImage;
use App\Model\UserFocus;

class ActivityService
{
    /**
     * 取消关注或者关注
     * @param $params
     * @param $is_active 1-开启 0-关闭
     * @return bool
     */
    public function activeFocus($params, $is_active)
    {
        /** @var $user_focus \App\Model\UserFocus */
        $user_focus = new UserFocus();
        $result = $user_focus->where('activity_id', $params['activeID'])->where('user_id', $params['userID'])->first();
        if (empty($result)) {
            $user_focus->user_id = $params['userID'];
            $user_focus->activity_id = $params['activeID'];
            $user_focus->is_active = $is_active;
            $bool = $user_focus->save();
        } else {
            $bool = $user_focus->where('activity_id', $params['activeID'])->where('user_id', $params['userID'])
                ->update(['is_active' => $is_active]);
        }
        if ($bool) {
            return [
                'status' => true,
                'msg' => 'success',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'msg' => '系统错误!',
                'info' => [],
            ];
        }
    }

    /**
     * 公益活动列表
     * @param $params
     * @return array
     */
    public function showList($params)
    {
        $page = !empty($params['page']) ?: 1;
        $count = !empty($params['count']) ?: 10;
        /** @var $activity \App\Model\Activity */
        $activity = new Activity();
        $activity_result = $activity->StatusOk()->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $count)->take($count)->get()->toArray();
        if (empty($activity_result)) {
            return [
                'status' => true,
                'msg' => '获取成功!',
                'info' => [],
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
                'create_time' => $activity_result_v['created_time'],
                'is_focus' => UserFocus::IS_ACTIVE_FALSE,
                'support' => $activity_result_v['focus_count'],
                'image_url' => ActivityImage::defaultImage(),
            ];
        }
        //获得关注信息
        $user_focus = UserFocus::where('activity_id', array_unique($activity_id_list))
            ->where('user_id', $params['userID'])->IsActiveTrue()->get()->toArray();
        $activity_id_is_true = [];
        foreach ($user_focus as $user_focus_v) {
            array_push($activity_id_is_true, $user_focus_v['activity_id']);
        }
        //获得图片
        $activity_image_result = ActivityImage::whereIn('activity_id', $activity_id_list)->PIC()
            ->groupBy('activity_id')->get()->toArray();
        $activity_image_info = [];
        foreach ($activity_image_result as $activity_image_result_v) {
            $activity_image_info[$activity_image_result_v['activity_id']] = $activity_image_result_v['image_url'];
        }
        foreach ($list as &$v) {
            if (in_array($v['active_id'], $activity_id_is_true)) {
                $v['is_focus'] = UserFocus::IS_ACTIVE_TRUE;
            }
            if (isset($activity_image_info[$v['active_id']])) {
                $v['image_url'] = $activity_image_info[$v['active_id']];
            }
        }
        $activity_count = $activity->StatusOk()->orderBy('created_at', 'desc')->count();
        $next_page = ((($page - 1) * $count) >= $activity_count) ? $page : $page + 1;
        $pager = [
            'page' => $page,
            'count' => $count,
            'total' => $activity_count,
            'next' => $next_page,
        ];
        return [
            'status' => true,
            'msg' => '获取成功!',
            'info' => $list,
            'pager' => $pager
        ];
    }

    /**
     * 公益活动详情
     * @param $params
     * @return array
     */
    public function showDetail($params)
    {
        if (empty($params['activeID'])) {
            return [
                'status' => false,
                'msg' => '活动ID不能为空!',
                'info' => [],
            ];
        }
        /** @var $activity \App\Model\Activity */
        $activity = new Activity();
        $activity_result = $activity->where('activity_id', $params['activeID'])->StatusOk()->first();
        if (empty($activity_result)) {
            return [
                'status' => false,
                'msg' => '活动不存在!',
                'info' => [],
            ];
        }
        $data = [];
        $data['active_id'] = $params['activeID'];
        $data['title'] = $activity_result->title;
        $data['content'] = $activity_result->desc;
        $data['create_time'] = $activity_result->created_at;
        $data['is_focus'] = UserFocus::userIsFocus($params['activeID'], $params['userID']);
        $data['support'] = $activity_result->focus_count;
        $data['image_url'] = ActivityImage::getImages($params['activeID']);
        $data['vedio_url'] = ActivityImage::getImages($params['activeID'], ActivityImage::TYPE_IMAGE_IS_GIF);
        $data['like_url'] = $activity_result->url;
        $fundraising = ActivityFundraising::where('activity_id', $params['activeID'])->first();
        $data['left_money'] = $fundraising->total_amount_price;
        $data['now_money'] = $fundraising->existing_price;
        $data['people_num'] = $fundraising->fundraising_count;
        return [
            'status' => true,
            'msg' => '获取成功!',
            'info' => $data,
        ];
    }
}