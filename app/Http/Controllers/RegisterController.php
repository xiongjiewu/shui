<?php namespace App\Http\Controllers;

use Input;

class RegisterController extends Controller
{
    public function register()
    {
        $cellphone = \Input::get('cellphone', null);
        $password = \Input::get('password', null);
        $password2 = \Input::get('password2', null);
        $verify = \Input::get('verify', null);
        $head = \Input::get('head', null);
        if (!$cellphone || !$password || !$password2 || !$verify || !$head) {
            return $this->fail('参数错误');
        }
        //检查登录信息
        $check = $this->check($cellphone, $password, $password2, $verify, $head);
        if ($check['status'] == 'error') {
            return $this->fail($check['message']);
        }
        return \Response::json([
            'code' => 0,
            'message' => '注册成功！',
            'userInfo' => [

            ],
        ]);
    }

    private function check($cellphone, $password, $password2, $verify, $head)
    {
        if ($password != $password2) {
            return [
                'status' => 'error',
                'message' => '2次密码不一致',
            ];
        }

        if ($verify) {
            return [
                'status' => 'error',
                'message' => '验证码不正确',
            ];
        }
    }
}