<?php namespace App\Http\Controllers;

use App\Application\User\UserService;
use App\Model\UserBase;
use App\Model\UserImage;
use Illuminate\Http\Request;
use Input;

class RegisterController extends Controller
{
    /**
     * 用户注册
     * @param Request $request
     * @return mixed
     */
    public function userRegister(Request $request)
    {
        $cellphone = $request->get('cellphone');
        $password = $request->get('password');
        $password2 = $request->get('password2');
        $verify = $request->get('verify');
        $head = $request->file('head');
        if (!$cellphone || !$password || !$password2 || !$verify) {
            return $this->fail('参数错误');
        }
        //检查登录信息
        $check = $this->check($cellphone, $password, $password2, $verify, $head, UserBase::TYPE_USER);
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
     * 商户注册
     * @param Request $request
     * @return mixed
     */
    public function BusinessRegister(Request $request)
    {
        $cellphone = $request->get('cellphone');
        $password = $request->get('password');
        $password2 = $request->get('password2');
        $verify = $request->get('verify');
        $head = $request->file('head');
        if (!$cellphone || !$password || !$password2 || !$verify) {
            return $this->fail('参数错误');
        }
        //检查登录信息
        $check = $this->check($cellphone, $password, $password2, $verify, $head, UserBase::TYPE_BUSINESS);
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
     * @param $type
     * @return array
     */
    private function check($cellphone, $password, $password2, $verify, $head, $type)
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
        if (!empty($head) && $head->isValid()) {
            $new_name = $this->updateFile($head);
            if ($new_name) {
                $path = $new_name;
            }
        }


        $image = [
            'url' => $path,
            'type' => UserImage::TYPE_LOGO,
        ];


        $register = (new UserService())->register($cellphone, $password, $type, $image);

        if ($register['status']) {
            return [
                'status' => 'ok',
                'message' => 'success',
                'userInfo' => $register['info'],
            ];
        }
        return [
            'status' => 'error',
            'message' => $register['message'],
            'userInfo' => [],
        ];
    }

    private function checkVerify($verify)
    {
        return true;
    }
}