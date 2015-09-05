<?php namespace App\Http\Controllers;

use App\Application\ActivityService;
use App\Application\OrderService;
use App\Application\WaterService;
use App\Model\UserFocus;
use \Illuminate\Http\Request;
use Input;
use App\Application\User\UserService;
use \Response;

class UserController extends BaseController
{
    /**
     * 反馈
     * @param Request $request
     * @return mixed
     */
    public function report(Request $request)
    {
        $check = (new UserService())->report($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '反馈成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 设置头像
     * @return mixed
     */
    public function newHead()
    {
        $head = Input::file('head');
        $check = (new UserService())->updateUserHead($head, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '修改成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 设置新密码
     * @param Request $request
     * @return mixed
     */
    public function newPassword(Request $request)
    {
        $check = (new UserService())->setNewPassword($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '修改成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 亲水包发送
     * @param Request $request
     * @return mixed
     */
    public function bagSend(Request $request)
    {
        $check = (new WaterService())->bagSend($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '发送成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 亲水包领取
     * @param Request $request
     * @return mixed
     */
    public function bagGet(Request $request)
    {
        $check = (new WaterService())->bagGet($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '领取成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 亲水包列表
     * @param Request $request
     * @return mixed
     */
    public function bagList(Request $request)
    {
        $check = (new WaterService())->bagList($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '搜索成功！',
                    'bagList' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 搜索用户或者店铺名
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $check = (new UserService())->search($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '搜索成功！',
                    'searchList' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 创建订单
     * @param Request $request
     * @return mixed
     */
    public function bankOrder(Request $request)
    {
        $check = (new OrderService())->bankOrder($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '提交成功！',
                    'order' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 确认订单
     * @param Request $request
     * @return mixed
     */
    public function bankSure(Request $request)
    {
        $check = (new OrderService())->bankSure($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '提交成功！',
                    'order' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 取消关注
     * @param Request $request
     * @return mixed
     */
    public function activeFocusCancel(Request $request)
    {
        $check = (new ActivityService())->activeFocus($request, $this->user_id, UserFocus::IS_ACTIVE_FALSE);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '取消关注成功！',
                    'order' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 获得关注
     * @param Request $request
     * @return mixed
     */
    public function activeFocus(Request $request)
    {
        $check = (new ActivityService())->activeFocus($request, $this->user_id, UserFocus::IS_ACTIVE_TRUE);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '关注成功！',
                    'order' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 水银行信息
     * @param Request $request
     * @return mixed
     */
    public function bankInfo(Request $request)
    {
        $check = (new WaterService())->bankInfo($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '关注成功！',
                    'order' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }
}