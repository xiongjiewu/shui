<?php namespace App\Http\Controllers;

use App\Application\UserService;
use App\Model\UserBase;
use App\Model\UserImage;
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
            'userInfo' => $check['userInfo'],
        ]);
    }

    /**
     * @param $cellphone
     * @param $password
     * @param $password2
     * @param $verify
     * @param $head
     * @return array
     */
    private function check($cellphone, $password, $password2, $verify, $head)
    {
        if ($password != $password2) {
            return [
                'status' => 'error',
                'message' => '2次密码不一致',
                'userInfo' => [],
            ];
        }

        if ($verify) {
            return [
                'status' => 'error',
                'message' => '验证码不正确',
                'userInfo' => [],
            ];
        }

        $image = [
            'url' => '',
            'type' => UserImage::TYPE_HEAD,
        ];

        $register = (new UserService())->register($cellphone, $password, UserBase::TYPE_BUSINESS, $image);
        if ($register['status']) {
            return [
                'status' => 'ok',
                'message' => 'success',
                'userInfo' => $register['info'],
            ];
        }
        return [
            'status' => 'error',
            'message' => $register['msg'],
            'userInfo' => [],
        ];
    }
}