<?php namespace App\Application;

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
}