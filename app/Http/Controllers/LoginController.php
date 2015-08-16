<?php namespace App\Http\Controllers;

use App\Application\UserService;
use Input;

class LoginController extends Controller
{
    public function login()
    {
        $cellphone = \Input::get('cellphone', null);
        $password = \Input::get('password', null);
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
                'message' => $check['msg'],
            ];
        }
    }
}