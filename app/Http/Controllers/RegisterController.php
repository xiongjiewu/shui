<?php namespace App\Http\Controllers;

use App\Application\User\UserService;
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
        $head = \Input::file('head', null);
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

        if (!$this->checkVerify($verify)) {
            return [
                'status' => 'error',
                'message' => '验证码不正确',
                'userInfo' => [],
            ];
        }

        $path = '';
        if (!$head->isValid()) {
            $client_name = $head->getClientOriginalName();
            $extension = $head->getClientOriginalExtension();
            $new_name = md5(date('ymdhis') . $client_name) . "." . $extension;
            $path = $head->move('storage/uploads', $new_name);
        }

        $image = [
            'url' => $path,
            'type' => UserImage::TYPE_HEAD,
        ];

        $register = (new UserService())->register($cellphone, $password, UserBase::TYPE_USER, $image);
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

    private function checkVerify($verify)
    {
        return true;
    }
}