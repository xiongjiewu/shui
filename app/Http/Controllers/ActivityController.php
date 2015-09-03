<?php namespace App\Http\Controllers;

use App\Application\ActivityService;
use \Response;
use \Input;

class ActivityController extends BaseController
{
    /**
     * 公益活动列表
     * @return mixed
     */
    public function activeList()
    {
        $params = Input::All();
        $check = (new ActivityService())->showList($params);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '反馈成功！',
                    'active_list' => $check['info'],
                    'pager' => $check['pager'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 公益详情
     * @return mixed
     */
    public function activeDetail()
    {
        $params = Input::All();
        $check = (new ActivityService())->showDetail($params);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '反馈成功！',
                    'active_list' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }
}