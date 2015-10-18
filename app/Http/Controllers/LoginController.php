<?php namespace App\Http\Controllers;

use App\Application\User\UserService;
use \Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * 用户登入
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $cellphone = $request->get('cellphone');
        $password = $request->get('password');
        if (!$cellphone || !$password) {
            return $this->fail('参数错误');
        }
        //检查登录信息
        $check = $this->check($cellphone, $password);
        if ($check['status'] == 'ok') {
            return \Response::json(
                [
                    'code' => 0,
                    'message' => '登录成功！',
                    'userInfo' => $check['userInfo'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    private function check($cellphone, $password)
    {
        $check = (new UserService())->login($cellphone, $password);
        if ($check['status']) {
            return [
                'status' => 'ok',
                'userInfo' => $check['info'],
            ];
        } else {
            return [
                'status' => 'error',
                'userInfo' => $check['message'],
            ];
        }
    }
}