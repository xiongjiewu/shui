<?php namespace App\Http\Controllers;

use App\Application\User\OrderService;
use App\Application\User\WaterService;
use Input;
use App\Application\User\UserService;
use \Response;

class UserController extends Controller
{
    public function __construct()
    {
        if (Input::has('userID')) {
            return $this->fail('用户ID不存在!');
        }
    }

    /**
     * 反馈
     * @params $user_id
     * @params $report
     * @return mixed
     */
    public function report()
    {
        $params = Input::All();
        $check = (new UserService())->report($params);
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
     * @params $user_id
     * @params $user_head
     * @return mixed
     */
    public function newHead()
    {
        $user_id = Input::get('userID');
        if (Input::hasFile('head')) {
            $head = Input::file('head');

        }
        $result = (new UserService())->updateUserHead($user_id, $head);
    }

    /**
     * 设置新密码
     * @return mixed
     */
    public function newPassword()
    {
        $params = Input::All();
        $check = (new UserService())->setNewPassword($params);
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
     * @return mixed
     */
    public function bagSend()
    {
        $params = Input::All();
        $check = (new WaterService())->bagSend($params);
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
     * @return mixed
     */
    public function bagGet()
    {
        $params = Input::All();
        $check = (new WaterService())->bagSend($params);
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
     * @return mixed
     */
    public function bagList()
    {
        $params = Input::All();
        $check = (new WaterService())->bagList($params);
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
     * @return mixed
     */
    public function search()
    {
        $params = Input::All();
        $check = (new UserService())->search($params);
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
     * @return mixed
     */
    public function bankOrder()
    {
        $params = Input::All();
        $check = (new OrderService())->bankOrder($params);
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
     * @return mixed
     */
    public function bankSure()
    {
        $params = Input::All();
        $check = (new OrderService())->bankSure($params);
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
}