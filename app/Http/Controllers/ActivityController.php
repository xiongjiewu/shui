<?php namespace App\Http\Controllers;

use App\Application\ActivityService;
use Illuminate\Http\Request;
use \Response;

class ActivityController extends BaseController
{
    /**
     * 公益活动列表
     * @param Request $request
     * @return mixed
     */
    public function activeList(Request $request)
    {
        $check = (new ActivityService())->showList($request, $this->user_id);
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
     * @param Request $request
     * @return mixed
     */
    public function activeDetail(Request $request)
    {
        $check = (new ActivityService())->showDetail($request, $this->user_id);
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

    /**
     * 公益捐款
     * @param Request $request
     * @return mixed
     */
    public function activeDonations(Request $request)
    {
        $check = (new ActivityService())->activeDonations($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '捐款成功！',
                    'active_list' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }
}