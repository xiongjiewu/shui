<?php namespace App\Http\Controllers;

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
                    'userInfo' => $check['user_info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    private function check($cellphone, $password)
    {
        return [
            'status' => 'ok',
            'user_info' => [

            ],
        ];
    }
}